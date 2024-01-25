@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    Edit Product
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('products.update', $product->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="cover_image" class="font-weight-bold">Cover Image</label>
                            <div>
                                <input id="cover_image" type="file" class="form-control @error('cover_image') is-invalid @enderror" name="cover_image">

                                @error('cover_image')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="name" class="font-weight-bold">Name</label>
                            <div>
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $product->name) }}" autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="price" class="font-weight-bold">Price</label>
                            <div>
                                <input id="price" type="number" class="form-control @error('price') is-invalid @enderror" name="price" value="{{ old('price', $product->price) }}" autocomplete="price">

                                @error('price')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description" class="font-weight-bold">Description</label>
                            <div>
                                <textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description">{{ old('description', $product->description) }}</textarea>

                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="is_published" class="font-weight-bold">Status</label>
                            <div>
                                <select id="is_published" class="form-control" name="is_published">
                                    <option value="1" @selected(old('is_published', $product->is_published == 1))>Published</option>
                                    <option value="0" @selected(old('is_published', $product->is_published == 0))>Not Published</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group mb-0">
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">
                                    Update Product
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.ckeditor.com/4.13.1/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('description');
    </script>
@endpush
