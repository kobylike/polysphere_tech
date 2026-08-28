<div>
    <div class="page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">CMS</a></li>
            <li class="breadcrumb-item active">
                <a href="javascript:void(0)">
                    {{ $serviceId ? 'Edit' : 'Add' }} Service
                </a>
            </li>
        </ol>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12">
                <div class="mb-3">
                    <ul class="d-flex align-items-center flex-wrap">
                        <li><a wire:navigate.hover href="{{ route('admin.services.index') }}" class="btn btn-primary"><i class="fa-solid fa-list me-1"></i> Services List</a></li>
                    </ul>
                </div>

                @if(session()->has('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form wire:submit.prevent="save" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-xl-8">
                            <!-- Name -->
                            <div class="mb-3">
                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control w-50" wire:model.live.debounce.500ms="name" placeholder="Service name">
                                @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <!-- Description -->
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" rows="8" wire:model="description" placeholder="Service description"></textarea>
                                @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <!-- Slug -->
                            <div class="filter cm-content-box box-primary">
                                <div class="content-title">
                                    <div class="cpa">Slug</div>
                                    <div class="tools"><a href="javascript:void(0);" class="expand SlideToolHeader"><i class="fal fa-angle-down"></i></a></div>
                                </div>
                                <div class="cm-content-body form excerpt">
                                    <div class="card-body">
                                        <input type="text" class="form-control" wire:model="slug" placeholder="URL-friendly version">
                                        @error('slug') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Icon -->
                            <div class="filter cm-content-box box-primary">
                                <div class="content-title">
                                    <div class="cpa">Icon (FontAwesome class)</div>
                                    <div class="tools"><a href="javascript:void(0);" class="expand SlideToolHeader"><i class="fal fa-angle-down"></i></a></div>
                                </div>
                                <div class="cm-content-body form excerpt">
                                    <div class="card-body">
                                        <input type="text" class="form-control" wire:model="icon" placeholder="e.g. fas fa-cloud">
                                        @error('icon') <span class="text-danger">{{ $message }}</span> @enderror
                                        <div class="form-text">Enter a FontAwesome class (e.g., fas fa-code, fa-solid fa-server).</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4">
                            <div class="right-sidebar-sticky">
                                <!-- Publish Box -->
                                <div class="filter cm-content-box box-primary">
                                    <div class="content-title">
                                        <div class="cpa">Status</div>
                                        <div class="tools"><a href="javascript:void(0);" class="expand SlideToolHeader"><i class="fal fa-angle-down"></i></a></div>
                                    </div>
                                    <div class="cm-content-body publish-content form excerpt">
                                        <div class="card-body">
                                            <select class="form-select" wire:model="status">
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                            </select>
                                            @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Featured Image -->
                                <div class="filter cm-content-box box-primary">
                                    <div class="content-title">
                                        <div class="cpa">Featured Image</div>
                                        <div class="tools"><a href="javascript:void(0);" class="expand SlideToolHeader"><i class="fal fa-angle-down"></i></a></div>
                                    </div>
                                    <div class="cm-content-body publish-content form excerpt">
                                        <div class="card-body">
                                            <div class="avatar-upload d-flex align-items-center">
                                                <div class="position-relative">
                                                    <div class="avatar-preview">
                                                        @if($existing_featured_image && !$featured_image)
                                                            <div style="width:150px; height:150px; background-image: url('{{ asset('storage/' . $existing_featured_image) }}'); background-size:cover; background-position:center; border-radius:8px;"></div>
                                                        @elseif($featured_image)
                                                            <div style="width:150px; height:150px; background-image: url('{{ $featured_image->temporaryUrl() }}'); background-size:cover; background-position:center; border-radius:8px;"></div>
                                                        @else
                                                            <div style="width:150px; height:150px; background-color:#f0f0f0; display:flex; align-items:center; justify-content:center; border-radius:8px;"><span class="text-muted">No image</span></div>
                                                        @endif
                                                    </div>
                                                    <div class="change-btn d-flex align-items-center flex-wrap mt-2">
                                                        <input type="file" id="featuredImageUpload" class="d-none" wire:model="featured_image" accept=".png,.jpg,.jpeg,.gif,.webp">
                                                        <label for="featuredImageUpload" class="btn btn-light ms-0">Choose Image</label>
                                                        @if($featured_image) <span class="ms-2 text-success">Uploaded</span> @endif
                                                    </div>
                                                    @error('featured_image') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Additional Images (up to 2) -->
                                <div class="filter cm-content-box box-primary">
                                    <div class="content-title">
                                        <div class="cpa">Additional Images (max 2)</div>
                                        <div class="tools"><a href="javascript:void(0);" class="expand SlideToolHeader"><i class="fal fa-angle-down"></i></a></div>
                                    </div>
                                    <div class="cm-content-body publish-content form excerpt">
                                        <div class="card-body">
                                            @for($i = 0; $i < 2; $i++)
                                                <div class="mb-3">
                                                    <label class="form-label">Image {{ $i+1 }}</label>
                                                    <input type="file" class="form-control" wire:model="additional_images.{{ $i }}" accept=".png,.jpg,.jpeg,.gif,.webp">
                                                    @error('additional_images.' . $i) <span class="text-danger">{{ $message }}</span> @enderror
                                                    @if(isset($existing_additional_images[$i]))
                                                        <div class="mt-1">
                                                            <span class="text-success">Current: {{ basename($existing_additional_images[$i]) }}</span>
                                                            <button type="button" class="btn btn-sm btn-danger ms-2" wire:click="removeAdditionalImage({{ $i }})">Remove</button>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endfor
                                            @error('additional_images') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="text-end mt-3">
                                    <button type="submit" class="btn btn-primary btn-sm">{{ $serviceId ? 'Update' : 'Save' }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>