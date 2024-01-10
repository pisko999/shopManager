@extends('layouts.app')
@section('content')
    @csrf
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">Stock</div>

                    <div class="card-body">
                        <div class="row form-group align-items-end">
                            <div class="col-4">
                                <label for="name">Jmeno:</label>
                            </div>
                            <div class="col-8">
                                <input type="text" id="yourInputField"
                                       data-api-url="https://api.scryfall.com/cards/autocomplete?"
                                       data-callback="yourCallbackFunction" class="form-control">
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col">
                                <form method="POST" action="{{ route('stock.save') }}">
                                    @csrf
                                    <input type="hidden" name="id" id="id">
                                    <div class="row form-group">
                                        <div class="col-4">
                                            <label for="all_product_id">All Product ID:</label>
                                        </div>
                                        <div class="col-8">
                                            <input type="text" name="all_product_id" id="all_product_id"
                                                   class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-4">
                                            <label for="initial_price">Initial Price:</label>
                                        </div>
                                        <div class="col-8">
                                            <input type="number" step="0.01" name="initial_price" id="initial_price"
                                                   class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-4">
                                            <label for="quantity">Quantity:</label>
                                        </div>
                                        <div class="col-8">
                                            <input type="number" name="quantity" id="quantity" class="form-control"
                                                   required>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-4">
                                            <label for="price">Price:</label>
                                        </div>
                                        <div class="col-8">
                                            <input type="number" step="0.01" name="price" id="price"
                                                   class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-4">
                                            <label for="language">Language:</label>
                                        </div>
                                        <div class="col-8">
                                            <input type="text" name="language" id="language" class="form-control"
                                                   required>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-4">
                                            <label for="state">State:</label>
                                        </div>
                                        <div class="col-8">
                                            <input type="text" name="state" id="state" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-4">
                                            <label for="idArticleMKM">ID Article MKM:</label>
                                        </div>
                                        <div class="col-8">
                                            <input type="text" name="idArticleMKM" id="idArticleMKM"
                                                   class="form-control">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-4">
                                            <label for="stock">Stock:</label>
                                        </div>
                                        <div class="col-8">
                                            <input type="text" name="stock" id="stock" class="form-control">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-4">
                                            <label for="isFoil">Is Foil:</label>
                                        </div>
                                        <div class="col-8">
                                            <input type="checkbox" name="isFoil" id="isFoil">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-4">
                                            <label for="signed">Signed:</label>
                                        </div>
                                        <div class="col-8">
                                            <input type="checkbox" name="signed" id="signed">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-4">
                                            <label for="playset">Playset:</label>
                                        </div>
                                        <div class="col-8">
                                            <input type="checkbox" name="playset" id="playset">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-4">
                                            <label for="altered">Altered:</label>
                                        </div>
                                        <div class="col-8">
                                            <input type="checkbox" name="altered" id="altered">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-4">
                                            <label for="on_sale">On Sale:</label>
                                        </div>
                                        <div class="col-8">
                                            <input type="checkbox" name="on_sale" id="on_sale">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-4">
                                            <label for="comments">Comments:</label>
                                        </div>
                                        <div class="col-8">
                                            <textarea name="comments" id="comments" class="form-control"
                                                      rows="3"></textarea>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-4">
                                            <label for="update">Update:</label>
                                        </div>
                                        <div class="col-8">
                                            <input type="datetime-local" name="update" id="update" class="form-control">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-4">
                                            <label for="is_new">Is New:</label>
                                        </div>
                                        <div class="col-8">
                                            <input type="checkbox" name="is_new" id="is_new">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-4">
                                        </div>
                                        <div class="col-8">
                                            <button type="submit" class="btn btn-primary">Create Stock</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('scripts')
    @parent
    <script>
        window.yourCallbackFunction = function(value) {
            console.log(value);
            // Do something with the value
        }
        document.addEventListener('DOMContentLoaded', (event) => {
            const search = new SearchInput("#yourInputField");
        });
    </script>
@endsection
