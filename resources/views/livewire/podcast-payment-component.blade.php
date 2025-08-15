<div>
    <form wire:submit.prevent="store">
        <div class="modal-body">
            <div class="row gap-3">
                <div class="col-12">
                    <h5>Order Details</h5>
                </div>
                <div class="col-12">
                    <label for="podcast-unit-price">Podcat Title</label>
                    <input type="text" class="form-control" id="podcast-unit-price" wire:model.lazy="title" readonly>
                </div>
                <div class="row">
                    <div class="col-6">
                        <label for="podcast-unit-price">Unit Price</label>
                        <input type="text" class="form-control" id="podcast-unit-price" wire:model.lazy="unit_price"
                            readonly>
                    </div>
                    <div class="col-6">
                        <label for="podcast-quantity">Quantity</label>
                        <input type="number" class="form-control" id="podcast-quantity" wire:model.lazy="quantity"
                            min="1">
                    </div>
                </div>
                <div class="col-12">
                    <h6>Total Price: KES. {{ number_format($total_price) }}</h6>
                    @if ($order_number)
                        <h6>Order Number: {{ $order_number }}</h6>
                    @endif
                </div>
                @if ($confirmed)
                    <div class="col-12">
                        <div class="alert alert-success" role="alert">
                            <h6>M-Pesa payment steps.</h6>
                            <ol>
                                <li>Enter phone number in the field provided below.</li>
                                <li>Click Pay Now</li>
                                <li>An Order number will be generated and an M-Pesa PIN request sent to your phone.</li>
                                <li>Confirm that the order number matches the one shown above.</li>
                                <li>Enter the MPesa PIN and complete payment.</li>
                            </ol>
                        </div>
                    </div>
                    <div class="col-12">
                        <label for="podcast-phone-number">M-Pesa Number</label>
                        <input type="text" class="form-control" id="podcast-phone-number"
                            wire:model.lazy="mpesa_number" placeholder="Enter M-Pesa number">
                    </div>
                @endif
            </div>
        </div>
        <div class="modal-footer">
            <span id="add-loader" class="form-text ms-2 {{ is_null($response_message) ? 'hidden' : '' }}">
                <i class="fas fa-spinner fa-spin"></i> <span>{{ $response_message }}</span>
            </span>
            <button type="submit" class="btn btn-secondary">{{ $confirmed ? 'Pay Now' : 'Confirm Order' }}</button>
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
        </div>
    </form>

</div>

@push('scripts')
    <script>
        $("#podcast-quantity").on('change', function(e) {
            Livewire.emit('updateTotalPrice');
        })
    </script>
@endpush
