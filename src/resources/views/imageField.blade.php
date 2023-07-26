<div class="container_remove_image">
        <div class="card mb-3">
            <div class="card-head">
                <div class="cancel_img_remove_container" style="display:none;">
                    <i class="fa fa-trash"></i>
                </div>
                <img class="card-img-top img-fluid img_preview"
                    @if( $item->$field )
                        src="{{ $item->getImageUrl($field) }}"
                    @else
                        src="{{ asset('vendor/fileupload/img/default.png') }}"
                    @endif
                    @if(isset($img_height))
                        height="{{$img_height}}"
                    @endif
                    @if(isset($img_width))
                        width="{{$img_width}}"
                    @endif
                >
            </div>
            <div class="card-body">
                @if( !isset($cant_delete) || $cant_delete==true  )
                    <div class="img_remove_container"
                        @if( !$item->$field )
                            style="display:none;"
                        @endif
                    >
                        <button class="btn-delete btn btn-xs btn-danger btn_delete_image" >
                            <i class="fa fa-trash mr-2"></i>{{ __('fileupload::fileupload.delete image') }}
                        </button>
                    </div>
                    <div class="cancel_img_remove_container" style="display:none;">
                        <button class="btn btn-info btn-xs btn_cancel_delete_image" >
                            <i class="fa fa-undo mr-2"></i>{{ __('fileupload::fileupload.do not delete')}}
                        </button>
                    </div>
                @endif
            </div>
        </div>

    <input type="hidden" name="delete_{{$field}}" class="h_image_delete" value="0">
    <div class="form-group">
        <label for="{{$field}}">{{$field}}</label>
        <input type="file" name="{{$field}}" class="form-control-file file_image_preview @error($field) is-invalid @enderror">
        @error($field)
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
