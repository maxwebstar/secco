@extends('layouts.admin.base')

@section('title', 'Manage permissions')

@section('content')

    <div class="row">
        <div class="col-md-6">
            <a class="btn btn-info" href="{{ route('admin.permission') }}" role="button">Show Permissions</a>
            <a class="btn btn-info" href="{{ route('admin.permission.group') }}" role="button">Show Permission Groups</a>
        </div>
    </div>
    <br>

    <div class="row">
        <div class="col-md-6">

            <div class="box box-default">
                <div class="box-header">
                </div>
                <div class="box-body">

                    <form class="form-horizontal" id="form-select-role" method="post" action="{{ route('admin.permission.manage') }}">

                        <div class="form-group {{ $errors->has('role_id') ? ' has-error' : '' }}">
                            <label for="selectRole" class="col-sm-2 control-label">Profile</label>
                            <div class="col-sm-4">
                                <select class="form-control" name="role_id" required>
                                    <option value="">Please select role</option>
                                    @foreach($dataRole as $iter)
                                        <option value="{{ $iter->id }}" {{ $role_id == $iter->id ? " selected" : "" }}>{{ $iter->display_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <button type="submit" id="submit-form-select-role" class="btn btn-info">Search</button>
                                @if($role_id)
                                    <button type="button" id="submit-form-set-permission" class="btn btn-primary">Save</button>
                                @endif
                            </div>

                            @if ($errors->has('role_id'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('role_id') }}</strong>
                                </span>
                            @endif
                        </div>

                        @csrf

                    </form>

                </div>
            </div>

        </div>
    </div>

    @if($role_id)
    <form id="form-set-permission" method="post" action="">

        @foreach($dataGroup as $group)
            @php $dataGroupPermission = $group->permissins; @endphp
            @if($dataGroupPermission->isNotEmpty())
                <div class="row">
                    <div class="col-md-6">
                        <div class="box box-default collapsed-box">

                            <div class="box-header with-border" data-widget="collapse">
                                <h3 class="box-title">{{ $group->display_name }}</h3>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool"><i class="fa fa-minus"></i>
                                    </button>
                                </div>
                                <!-- /.box-tools -->
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body" style="">
                                <div class="form-group">
                                    @foreach($dataGroupPermission as $iter)

                                        <div class="checkbox">
                                            <label>
                                                <input name="permission[{{ $iter->id }}]" type="checkbox" value="1" {{ empty($dataRolePermission[$role_id][$iter->id]) ? "" : " checked" }}>
                                                {{ $iter->display_name }}
                                            </label>
                                        </div>

                                    @endforeach
                                </div>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                </div>
            @endif
        @endforeach

        @csrf
        <input type="hidden" name="role_id" value="{{ $role_id }}">

    </form>
    @endif

@endsection

@push('script')
<script>
    $(function() {

        $( "#form-select-role" ).on( "click", "#submit-form-set-permission", function() {

            var formPermission = $('#form-set-permission');

            $.ajax({
                url : "{{ route('admin.permission.manage.save') }}",
                data : formPermission.serialize(),
                async : true,
                method : 'post',
                dataType : 'json',
                beforeSend : function (){
                },
                success : function(response){

                    switch(response.status){
                        case 'not_valid' :

                            var notValidMessage = '';
                            $.each(response.param, function(errorHolder, errorMassage){
                                $.each(errorMassage, function(key, message){
                                    notValidMessage += '<p>'+message+'</p>';
                                });
                            });

                            jsAlertHtml.set(
                                'danger',
                                'Data is not valid.',
                                notValidMessage,
                                0);
                            $("section.content").prepend(jsAlertHtml.get());

                        case 'saved' :

                            jsAlertHtml.set(
                                response.alert.type,
                                response.alert.title,
                                response.alert.message,
                                response.alert.hide);
                            $("section.content").prepend(jsAlertHtml.get());

                            break;
                        default :
                            break;
                    }
                },
                error : function(response){

                    jsAlertHtml.set(
                        response.alert.type,
                        response.alert.title,
                        response.alert.message,
                        response.alert.hide);

                    $("section.content").prepend(jsAlertHtml.get());
                }
            });


        });


    });
</script>
@endpush