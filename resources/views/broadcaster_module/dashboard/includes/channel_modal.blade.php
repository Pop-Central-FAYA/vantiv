@foreach($user_channel_with_other_details as $user_channel_details)
    @if($user_channel_details['channel_details']->channel == 'TV')
        <div class="modal_contain" id="modal_channel_tv">
            <h2 style="text-align: center">TV Brands</h2>
            <div class="mb4 clearfix pt4 mb4">
                @foreach($user_channel_details['companies'] as $company)
                    <div class="column col_4">
                        <img style="width: 150px; height: 100px;" src="{{ asset($company->logo ? $company->logo : '') }}" alt="{{ $company->name }}">
                    </div>
                @endforeach
            </div>
        </div>
    @elseif($user_channel_details['channel_details']->channel == 'Radio')
        <div class="modal_contain" id="modal_channel_radio">
            <h2 style="text-align: center">Radio Brands</h2>
            <div class="mb4 clearfix pt4 mb4">
                @foreach($user_channel_details['companies'] as $company)
                    <div class="column col_4">
                        <img style="width: 150px; height: 100px;" src="{{ asset($company->logo ? $company->logo : '') }}" alt="{{ $company->name }}">
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endforeach
