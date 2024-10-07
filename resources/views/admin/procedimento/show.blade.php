@extends('admin.master')

@section('conteudo')

<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">

    <!-- begin:: Subheader -->
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                Visualiar Empresa </h3>
            <span class="kt-subheader__separator kt-hidden"></span>
            <div class="kt-subheader__breadcrumbs">
                <a href="#" class="kt-subheader__breadcrumbs-home"><i class="flaticon2-shelter"></i></a>
                <span class="kt-subheader__breadcrumbs-separator"></span>
                <a href="" class="kt-subheader__breadcrumbs-link">
                    Visualizar 
                </a>
                <!-- <span class="kt-subheader__breadcrumbs-link kt-subheader__breadcrumbs-link--active">Active link</span> -->
            </div>
        </div>
    </div>
    <!-- end:: Subheader -->

    <!-- begin:: Content -->
    <div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">

        <!--begin::Portlet-->
        <div class="row">
            <div class="col-lg-12">
                <!--begin::Portlet-->
                <div class="kt-portlet">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                               Visualizar Empresa
                            </h3>
                        </div>
                    </div>
                    <!--begin::Form-->
                        <div class="kt-portlet__body">
                            <div class="form-group row">
                                <div class="col-lg-4">
                                    <div class="visualizar">
                                        <label class="visualizar-titulo">Razão Social:</label>
                                        {{ $empresa->razaosocial }}
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="visualizar">
                                        <label class="visualizar-titulo">Nome Fantasia:</label>
                                        {{ $empresa->nomefantasia }}
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="visualizar">
                                        <label class="visualizar-titulo">NIF:</label>
                                        {{ $empresa->nif }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-4">
                                    <div class="visualizar">
                                        <label class="visualizar-titulo">Ativo:</label>
                                       {{ $empresa->ativo== 'S' ? 'Sim' : 'Não' }}
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="visualizar">
                                        <label class="visualizar-titulo">Visualizar Rel Atendimento:</label>
                                        {{ $empresa->visualizarrelatendimento== 'S' ? 'Sim' : 'Não' }}
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="visualizar">
                                        <label class="visualizar-titulo">Seguradora:</label>
                                        {{ $seguradora->seguradora }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-4">
                                    <div class="visualizar">
                                        <label class="visualizar-titulo">Ramo Atividade:</label>
                                        {{ $empresa->ramoatividade }}
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="visualizar">
                                        <label class="visualizar-titulo">Morada:</label>
                                        {{ $empresa->morada }}
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="visualizar">
                                        <label class="visualizar-titulo">Corretor:</label>
                                        {{ $empresa->corretor }}
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-lg-10">
                                    <div class="visualizar">
                                        <label class="visualizar-titulo">Observação:</label>
                                        {{ $empresa->observacao }}
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-lg-2"></div>
                                <div class="col-lg-8">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                        <tr>
                                            <th>Tipo</th>
                                            <th>Contato</th>
                                            <th>Principal </th>
                                        </tr>
                                        </thead>
                                        <tbody id="table-body">
                                    @forelse ($contatoArray as $contato)
                                    <tr>
                                        <td>{{ $contato['tipo']}} </td>
                                        <td>{{ $contato['descricao'] }}</td>
                                        <td>{{ $contato['flg_principal'] === 'T' ? 'Sim' : 'Não' }}</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="10">Nenhum Contato encontrado</td></tr>
                                    @endforelse

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="kt-portlet__foot">
                            <div class="kt-form__actions">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <a href="{{ route('empresa.index') }}"  class="btn btn-outline-danger">Voltar</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>

                <!--end::Portlet-->
            </div>
        </div>
    </div>
    <!-- end:: Content -->
</div>
@endsection