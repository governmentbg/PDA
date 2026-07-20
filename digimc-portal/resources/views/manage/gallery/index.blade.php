@extends('layouts.app')

@section('content')

    <section class="section">
        <div class="container">
            <h1 class="pull-left"> Колекции </h1>

            <div class="col pb-5">
            {{ html()->form(action: route('manage.gallery.index'), method: 'get')->open() }}
            @include('manage.gallery.filter_fields')
            {{ html()->form()->close() }}
            </div>

            <div class="content">
                <div class="clearfix"></div>

                @include('flash::message')

                <div class="clearfix"></div>
                <div class="box box-primary">
                    <div class="box-body">
                        @include('manage.gallery.table')
                    </div>

                    <div class="modal fade" id="actionModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <form method="POST" id="actionForm">
                                @csrf
                                @method('PATCH')
                                <div class="modal-content border-0 shadow-sm">
                                    <div class="modal-header text-white" id="modalHeader">
                                        <h5 class="modal-title" id="modalTitle"><i class="fa fa-exclamation-triangle me-2"></i></h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Моля, въведете причината. Това ще бъде изпратено на потребителя по имейл.</p>
                                        <textarea name="reason" class="form-control form-control-lg" rows="4" placeholder="Въведете причина..." required></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn" id="modalSubmitBtn">
                                            <i class="fa fa-times-circle me-1"></i> Действие
                                        </button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                            <i class="fa fa-ban me-1"></i> Отказ
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                </div>

            </div>
        </div>
        <div class="modal fade" id="editGalleryModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Редакция на галерия</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="editGalleryForm" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Име</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Описание</label>
                                <textarea class="form-control" name="description" rows="4"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Затвори</button>
                            <button type="submit" class="btn btn-success">Запази</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection



