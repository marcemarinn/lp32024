@extends('layouts.app')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Ventas</h1>
            </div>
            <div class="col-sm-6">
                @if (
                !empty($cajaAbierta) && \Carbon\Carbon::parse($cajaAbierta->ape_fecha)->format('Y-m-d') == \Carbon\Carbon::now()->format('Y-m-d') )
                <a class="btn btn-primary float-right"
                    href="{{ route('ventas.create') }}">
                    Nueva Venta
                </a>
                @endif

                @if (empty($cajaAbierta))
                <a class="btn btn-default float-right" data-toggle="modal"
                    data-target="#apertura"
                    style="margin-right: 10px" href="#">
                    Abrir Caja
                </a>
                @endif

                @if (!empty($cajaAbierta) &&
                \Carbon\Carbon::parse($cajaAbierta->ape_fecha)->format('Y-m-d')
                <= \Carbon\Carbon::now()->format('Y-m-d'))
                    <a class="btn btn-success float-right" id="cerrar"
                        data-id="{{ isset($cajaAbierta) ? $cajaAbierta->ape_nro : null }}"
                        href="#"
                        style="margin-right: 10px">
                        <i class="fa fa-plus"></i>
                        Cerrar Caja
                    </a>
                @endif
            </div>

        </div>
    </div>
</section>

<div class="content px-3">

    @include('sweetalert::alert')

    <div class="clearfix"></div>

    <div class="card">
        @include('ventas.table')
    </div>
</div>
<!-- llamar a modal -->
@include('ventas.modal_apertura')

<!-- llamar a modal cerrar -->
@include('ventas.modal_cerrar')

@endsection

@push('page_scripts')
<script type="text/javascript">
    $(document).ready(function() {
        $("#cerrar").on("click", function(e) {
            e.preventDefault();
            e.stopPropagation();

            let $this = this;

            if ($this.classList.contains('disabled')) {
                return false;
            }
            // Modal
            let modalRef = document.getElementById('cerrar-caja');
            const id = $this.getAttribute('data-id');
            // Obtener datos del cierre de caja a través de la API que esta en el controlador de apertura y cierre caja
            const url = "{{ url('apertura_cierre/editCierre') }}/" + id;

            fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(res => {
                /** recuperar valores y mostrarlos */
                // Si la respuesta es success True proceso el resultado devuelto por la funcion
                if (res.success) {
                    document.getElementById("fecha").value = res.apertura.ape_fecha;
                    document.getElementById("monto_cierre").value = formatMoney(res.total);
                    document.getElementById("monto_apertura").value = formatMoney(res.apertura.ape_monto_inicial);
                    document.getElementById("caj_cod").value = res.apertura.caj_cod;
                    // Dispara el evento 'change' manualmente
                    const event = new Event('change');
                    document.getElementById("caj_cod").dispatchEvent(event);

                    const formUrl = "{{ url('apertura_cierre/cerrar_caja') }}/" + id;
                    // Asignar la URL del formulario al modal para que redireccione al método PATCH de la API de cierre de caja
                    modalRef.querySelector('#form-apertura').setAttribute("action", formUrl);
                    $("#cerrar-caja").modal("show");//mostrar modal
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: res.message,
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Hubo un problema al procesar la solicitud.',
                    icon: 'error',
                    confirmButtonText: 'Ok'
                });
            });
        });

    });

    function formatMoney (n, c, d, t) {
            let s, i, j;
            c = isNaN(c = Math.abs(c)) ? 0 : c;
            d = d === undefined ? "," : d;
            t = t === undefined ? "." : t;
            s = n < 0 ? "-" : "";
            i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c)));
            j = (j = i.length) > 3 ? j % 3 : 0;
            return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) +
                (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
    }
</script>
@endpush
