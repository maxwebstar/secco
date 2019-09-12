@extends('layouts.admin.base')

@section('content')

<div id="modal-set-gov-date" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Set Governing Date</h4>
            </div>
            <div class="modal-body">

                <div class="alert alert-danger">Governing Date is missing for this IO </div>

                <div class="form-group">
                    <label for="" class="">Date</label>
                    <div class="input-group input-group-sm">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar" aria-hidden="true"></i>
                            </span>
                        <input type="text" id="datepicker" class="form-control" name="newGovDate" placeholder="yyyy-mm-dd">
                    </div>
                </div>

                <span id="dateError" style="display: none;color: red;"> Invalid Date.(mm/dd/yyyy or mm-dd-yyyy)</span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-sm saveGovDate">Save</button>
                <button type="button" class="btn bg-default btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


{{--<div class="row">--}}

    {{--<div class="col-md-12">--}}

        {{--<div class="form-group">--}}
            {{--<label for="" class="">Date</label>--}}
            {{--<div class="input-group input-group-sm">--}}
                {{--<span class="input-group-addon">--}}
                    {{--<i class="fa fa-calendar" aria-hidden="true"></i>--}}
                {{--</span>--}}
                {{--<input type="text" id="datepicker" class="form-control" name="newGovDate" placeholder="yyyy-mm-dd">--}}
            {{--</div>--}}
        {{--</div>--}}

    {{--</div>--}}
{{--</div>--}}

@endsection

@push('script')
<script>

    $(function() {

        $("#modal-set-gov-date").modal('show');

        //$.noConflict();
//        $("#datepicker").datepicker();

        $("input[name=newGovDate]").datepicker({
            formatDate : "yyyy-mm-dd"
        });

    });

</script>
@endpush