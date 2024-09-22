<!-- Name Field -->
<div class="form-group col-sm-12">
    {!! Form::label('name', 'Nombre:') !!}
    {!! Form::text('name', null, ['class' => 'form-control', 'maxlength' => 255, 'required' => 'required']) !!}
</div>

<div class="form-group col-sm-12">
    <div class="table-responsive">
        <table class="table table-bordered">
            <tr>
                <th>
                    <input type="checkbox" name="all" id="checkall" />
                     Marcar Todos
                </th>
                <th>Permiso</th>
            </tr>
            @foreach ($permissions as $permission)
                @php
                    $sel = '';
                    if (isset($role) and $role->hasPermissionTo($permission->name)) {
                        $sel = 'checked="checked"';
                    }
                @endphp
                <tr>
                    <td>
                        <input type="checkbox" name="permission[]" class="child"
                            value="{!! $permission->id !!}"
                            {!! $sel !!}
                        >
                    </td>
                    <td>
                        {!! $permission->name !!}
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
</div>

@push('page_scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $("#checkall").click(function() {
                $(".child").prop("checked", this.checked);
            });

            $('.child').click(function() {
                if ($('.child:checked').length == $('.child').length) {
                    $('#parent').prop('checked', true);
                } else {
                    $('#parent').prop('checked', false);
                }
            });
        });
    </script>
@endpush
