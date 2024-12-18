<?php

namespace App\Http\Controllers\admin;

use App\Models\Plano;
use App\Models\Apolice;
use App\Models\Empresa;
use App\Models\Seguradora;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreApoliceRequest;
use App\Services\ExportacaoService;
use App\Exports\ApoliceExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RelatorioResulmoApoliceExport;

use Illuminate\Support\Facades\DB;


class ApoliceController extends Controller
{
    /**
     * Exibe a lista de apólices.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {

        // Paginação de 15 apólices por página
        $apolices = Apolice::paginate(15);
        return view('admin.apolice.grid', compact('apolices'));
    }

    /**
     * Mostra o formulário de criação de uma nova apólice.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {

        // Buscar as seguradoras ativas e criptografar seus IDs
        $seguradoras = Seguradora::where('ativo', 'S')->get()->map(function ($seguradora) {
            $seguradora->encrypted_id = encrypitar($seguradora['id_seguradora']);
            return $seguradora;
        });
        // Buscar as seguradoras ativas e criptografar seus IDs
        $empresas = Empresa::where('ativo', 'S')->get()->map(function ($empresa) {
            $empresa->encrypted_id = encrypitar($empresa['id_seguradora']);
            return $empresa;
        });
        $planos = Plano::all();
        // Retorna a view para criar uma nova apólice
        return view('admin.apolice.create', compact('seguradoras', 'empresas', 'planos'));
    }

    /**
     * Armazena uma nova apólice no banco de dados.
     *
     * @param  \App\Http\Requests\StoreApoliceRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreApoliceRequest $request)
    {
        // Prepara os dados da apólice
        $apolicesData = $request->all();


        // Mapeia os campos de checkbox
        $checkboxFields = [
            'excecaoAtendimentoObstetricia',
            'renovacaoLimite',
            'status',
            'permiteReembolso',
            'seguirTipoRegra',
            'redeInternacional',
            'utilizaDigital',
            'regraCronico',
            're-graIdoso',
            'resseguro',
            'liberarGuiaGratuita',
        ];

        // Converte os valores para 'S' ou 'N'
        foreach ($checkboxFields as $field) {
            $apolicesData[$field] = $request->has($field) ? 'S' : 'N';
        }

        // Cria a nova apólice no banco de dados
        $apolice = Apolice::create($apolicesData);

        // Redireciona para a lista de apólices com uma mensagem de sucesso
        return redirect()->route('apolice.index')->with('success', 'Apólice criada com sucesso.');
    }

    /**
     * Exibe uma apólice específica.
     *
     * @param  \App\Models\Apolice  $apolice
     * @return \Illuminate\View\View
     */
    public function show(Apolice $apolice)
    {
        // Converte o JSON do contato (se aplicável) para um array
        $contatoArray = json_decode($apolice->contato, true);

        // Retorna a view 'show' com os dados da apólice
        return view('admin.apolice.show', compact('apolice', 'contatoArray'));
    }

    /**
     * Mostra o formulário de edição de uma apólice.
     *
     * @param  \App\Models\Apolice  $apolice
     * @return \Illuminate\View\View
     */
    public function edit(Apolice $apolice)
    {
        // Retorna a view 'edit' com os dados da apólice para edição
        return view('admin.apolice.edit', compact('apolice'));
    }

    /**
     * Atualiza uma apólice existente no banco de dados.
     *
     * @param  \App\Http\Requests\StoreApoliceRequest  $request
     * @param  \App\Models\Apolice  $apolice
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(StoreApoliceRequest $request, Apolice $apolice)
    {
        // Prepara os dados da apólice para atualização
        $apoliceData = $request->all();

          // Mapeia os campos de checkbox
          $checkboxFields = [
            'excecaoAtendimentoObstetricia',
            'renovacaoLimite',
            'status',
            'permiteReembolso',
            'seguirTipoRegra',
            'redeInternacional',
            'utilizaDigital',
            'regraCronico',
            're-graIdoso',
            'resseguro',
            'liberarGuiaGratuita',
        ];

        // Converte os valores para 'S' ou 'N'
        foreach ($checkboxFields as $field) {
            $apolicesData[$field] = $request->has($field) ? 'S' : 'N';
        }


        // Atualiza a apólice no banco de dados
        $apolice->update($apoliceData);

        // Redireciona para a lista de apólices com uma mensagem de sucesso
        return redirect()->route('apolice.index')->with('success', 'Apólice atualizada com sucesso.');
    }


    public function relatorio(){

        // Buscar as seguradoras ativas e criptografar seus IDs
        $seguradoras = Seguradora::where('ativo', 'S')->get()->map(function ($seguradora) {
            $seguradora->encrypted_id = encrypitar($seguradora['id_seguradora']);
            return $seguradora;
        });
        // Buscar as seguradoras ativas e criptografar seus IDs
        $empresas = Empresa::where('ativo', 'S')->get()->map(function ($empresa) {
            $empresa->encrypted_id = encrypitar($empresa['id_seguradora']);
            return $empresa;
        });
        return view('admin.apolice.relatorio', compact('seguradoras','empresas'));
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
    public function export(Request $request)
    {

        $request->validate([
            'dataInicioCobertura' => 'required',
            'dataFimCobertura' => 'required',
        ]);

        return Excel::download(new ApoliceExport($request->dataInicioCobertura, $request->dataFimCobertura), 'relatorio.xlsx');
    }



    /**
     * Método responsável por exportar o relatório.
     *
     * Este método é chamado quando uma requisição HTTP é feita para exportar um relatório.
     * Ele usa o serviço relatorioResulmoApolice para gerar e baixar o arquivo.
     *
     * @param Request $request Objeto contendo os dados da requisição HTTP.
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse Resposta contendo o arquivo para download.
     */
    public function relatorioResulmoApolice(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {

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


        // Validação dos campos de data
        $request->validate([
            'datainicio' => 'required|date_format:d/m/Y',
            'datafim'    => 'required|date_format:d/m/Y'
        ]);
        $request->seguradora = decrypitar($request->seguradora);
        $request->validate(['seguradora' => 'required']);

        return Excel::download(new RelatorioResulmoApoliceExport($request->seguradora ,$dataInicio ?? null,$dataFim ?? null), 'relatorio_Resumo_apolice.xlsx');

    }
}
