@section("scripts")
    <script type="text/javascript">
        $(document).ready(function () {
            $("#btnAceptarCita").click(function () {
                var id = $("#btnAceptarCita").attr("cita");
                aceptaRechaza(id, true)
            });

            $("#btnrechazarcita").click(function () {
                var id = $("#btnAceptarCita").attr("cita");
                aceptaRechaza(id, false)
            });

            function aceptaRechaza(id, acepta) {
                modal.showPleaseWait();

                $.ajax({
                    url: "{{route('citas.acepta')}}",
                    type: "POST",
                    data: {id: id, acepta: acepta},
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                }).done(function () {
                    location.reload();
                }).always(function () {
                    modal.hidePleaseWait();
                })
            }
        })
    </script>
@endsection
@section('content')
    <div class="container">
        <h1>Citas ofrecidas</h1>
        <table class="table">
            <th>
            <td></td>
            <td>Mi mascota</td>
            <td>Mascota solicitante</td>
            <td>Raza</td>
            <td>Tipo</td>
            <td></td>

            </th>
            @foreach($citas as $cita)
                <tr>
                    <td>
                        @if(empty($cita->imagenOfrecido))
                            <img alt="{{$cita->nombreOfrecido}}"
                                 src="/img/no-avatar.png"
                                 class="twPc-avatarImg">
                        @else
                            <img alt="{{$cita->nombreOfrecido}}"
                                 src="{{$cita->imagenOfrecido}}"
                                 class="twPc-avatarImg">
                        @endif
                    </td>
                    <td>{{$cita->nombreOfrecido}}</td>
                    <td>{{$cita->nombreBuscando}}</td>
                    <td>{{$cita->raza}}</td>
                    <td>{{$cita->tipo}}</td>
                    <td>
                        <div class="btn-group">
                            <button type="button" id="btnAceptarCita" class="btn btn-success" cita="{{$cita->id}}">
                                Aceptar
                            </button>
                            <button type="button" id="btnRechazarCita" class="btn btn-danger" cita="{{$cita->id}}">
                                Rechazar
                            </button>
                        </div>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection