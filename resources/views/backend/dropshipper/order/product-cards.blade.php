<div class="row row-cols-1 row-cols-md-4" id="products-container">
    @forelse ($products as $item)
        @php $product = $item->product; @endphp
        @if($product)
            <div class="col mb-4">
                <div class="card" style="height:380px;">
                    <img class="card-img-top" src="{{ url('upload/product_images/' . $product->image) }}" alt="{{ $product->slug }}" style="height:260px;">
                    <div class="card-body">
                        <h5 title="{{ $product->name }}">{{ \Illuminate\Support\Str::limit($product->name, 25) }}</h5>

                        <div class="product-price">
                            @if(!empty($product->discount))
                                <span class="price text-danger">
                                    @if($product->discount_type == 1)
                                        &#2547; {{ $product->price - ($product->price * $product->discount / 100) }}
                                    @else
                                        &#2547; {{ $product->price - $product->discount }}
                                    @endif
                                </span>
                            @else
                                <span class="price text-danger">&#2547; {{ $product->price }}</span>
                            @endif
                        </div>

                        <div class="d-flex justify-content-center mt-2">
                            <button type="button" onclick="addToMyCart(this.id)" id="{{ $product->id }}" class="btn btn-info btn-sm">Add to Cart</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @empty
        <p class="text-center w-100">No products found!</p>
    @endforelse
</div>
