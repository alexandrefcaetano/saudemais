<?php
namespace App\Http\Controllers\admin;

use App\Exports\RelatorioCencusCronicoExport;
use App\Exports\RelatorioCencusSeguroExport;
use App\Exports\RelatorioMaioresUtilizadoresExport;
use App\Http\Controllers\Controller;
use App\Imports\ClientesImport;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel; // Se você estiver usando Laravel Excel
use App\Imports\BeneficiariosImport; // Defina sua classe de importação se estiver usando Laravel Excel



class ClienteController extends Controller
{


    public function index(Request $request)
    {

        // Inicia a query base para Especialidade
        $query = Cliente::query();
        // Paginação de acordo com a quantidade selecionada
        $registrosPorPagina = $request->input('registrosPorPagina', 15);
        $clientes = $query->orderBy('id_cliente', 'desc')->paginate($registrosPorPagina);
        return view('admin.cliente.grid', compact('clientes','registrosPorPagina'));

    }

    public function createImportacao()
    {
        $linhas_analise = array();
        return view('admin.cliente.createImportacao',compact('linhas_analise')); // View da tela de importação cliente
    }


    public function importarClientes(Request $request)
    {
        $file = $request->file('arquivo');

        // Verifica se o arquivo é válido e do tipo permitido
        if (!$file->isValid() || !in_array($file->getClientOriginalExtension(), ['xlsx', 'xls', 'csv'])) {
            return redirect()->back()->withErrors(['arquivo' => 'Arquivo inválido. Certifique-se de que é um arquivo Excel ou CSV.']);
        }

        // Cria a instância do importador de clientes
        $import = new ClientesImport();

        try {
            // Importa o arquivo
            Excel::import($import, $file);

            // Obtém os erros usando o método getErrors()
            $linhas_arquivo = [
                'arquivo_id' => null,
                'nome_arquivo' => $file->getClientOriginalName(),
            ];

            $linhas_erros =  $import->getErrors();
            $linhas_analise = array_merge($linhas_arquivo, $linhas_erros);

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            // Retorna a visualização de erros
            return view('importacao.erros', ['erros' => $e->failures()]);
        }

        // Retorna a análise completa para a visualização
        return view('admin.cliente.createImportacao', compact('linhas_analise'));
    }


    public function confirmarImportacao(Request $request)
    {

        if (empty($registrosValidos)) {
            return redirect()->back()->with('error', 'Não há registros válidos para importar.');
        }

        // Salva cada registro válido no banco de dados
        foreach ($registrosValidos as $registro) {
            $registro->save();
        }

        return redirect()->back()->with('success', 'Registros válidos importados com sucesso.');
    }

    public function download($filename)
    {

        $filePath = storage_path('app/public/modelos/' . $filename); // Se o arquivo estiver na pasta "storage/app/public"

        // Verifique se o arquivo existe
        if (!file_exists($filePath)) {
            abort(404, 'Arquivo não encontrado.');
        }
        // Retorna o arquivo para download
        return response()->download($filePath);
    }


    /**
     * Método responsável por exportar o relatório.
     *
     * Este método é chamado quando uma requisição HTTP é feita para exportar um relatório.
     * Ele usa o serviço RelatorioAdministrativoService para gerar e baixar o arquivo.
     *
     * @param Request $request Objeto contendo os dados da requisição HTTP.
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse Resposta contendo o arquivo para download.
     */
    public function relatorioCensusSeguro(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $request->seguradora = decrypitar($request->seguradora);

        $request->validate(['seguradora' => 'required']);

        return Excel::download(new RelatorioCencusSeguroExport($request->seguradora,  $request->empresa ?? null, $request->apolice ?? null, $request->beneficiarionecessitaautorizacao ?? null), 'relatorio.xlsx');


    }

    /**
     * Método responsável por exportar o relatório.
     *
     * @param Request $request Objeto contendo os dados da requisição HTTP.
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse Resposta contendo o arquivo para download.
     */
    public function relatorioMaioresUtilizadores(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        // Validação dos campos de data
        $request->validate([
            'datainicio' => 'required|date_format:d/m/Y',
            'datafim' => 'required|date_format:d/m/Y'
        ]);

        // Converte as datas de d/m/Y para Y-m-d para serem usadas no banco de dados
        $dataInicio = \DateTime::createFromFormat('d/m/Y', $request->datainicio);
        $dataFim = \DateTime::createFromFormat('d/m/Y', $request->datafim);

        // Verifica se as datas foram convertidas corretamente
        if (!$dataInicio || !$dataFim) {
            // Retorna erro caso as datas não sejam válidas
            return back()->withErrors(['As datas informadas são inválidas.']);
        }

        // Formata as datas para Y-m-d
        $dataInicio = $dataInicio->format('Y-m-d');
        $dataFim = $dataFim->format('Y-m-d');

        // Exporta o relatório usando o Excel e retorna o arquivo para download
        return Excel::download(
            new RelatorioMaioresUtilizadoresExport(
                $request->empresa_id ?? null,
                $dataInicio,
                $dataFim
            ),
            'RelatorioMaioresUtilizadores.xlsx'
        );
    }


    /**
     * Método responsável por exportar o relatório.
     *
     * Este método é chamado quando uma requisição HTTP é feita para exportar um relatório.
     * Ele usa o serviço RelatorioAdministrativoService para gerar e baixar o arquivo.
     *
     * @param Request $request Objeto contendo os dados da requisição HTTP.
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse Resposta contendo o arquivo para download.
     */
    public function relatorioCencusCronico(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $request->seguradora = decrypitar($request->seguradora);
        $request->validate(['seguradora' => 'required']);

        $nomeRelatorio = 'relatorio_cronico'.date("Ymd_hms").'.xlsx';
        return Excel::download(new RelatorioCencusCronicoExport($request->seguradora,  $request->empresa ?? null, $request->apolice ?? null, $request->cobertura ?? null), $nomeRelatorio);


    }



}
