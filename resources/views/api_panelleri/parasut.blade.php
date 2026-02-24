<div class="form" style="display: none;" id="parasut">
    <div class="card box">
        <div class="card-header page-header">
            <h4>Paraşüt</h4>
        </div>
        
        <div class="card-body">
            <div class="d-flex gap-3">
                <input type="text" class="form-control" value="{{ @$kart_veri->CLIENT_ID }}" name="CLIENT_ID" placeholder="Client ID">
                <input type="text" class="form-control" value="{{ @$kart_veri->CLIENT_SECRET }}" name="CLIENT_SECRET" placeholder="Client Secret">
            </div>
        </div>
    </div>
</div>
