
<div class="modal_contain profile" style="width: 1000px;" id="profileme">
    <div class="sub_header clearfix mb pt">
        <div class="column col_6">
            <h2 class="sub_header">Edit Profile</h2>
        </div>
    </div>
    <!-- main stats -->
    <div class="the_frame clearfix mb ">
        <form action="{{ route('profile.update.details') }}" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="margin_center col_11 clearfix pt4">
                <div class="column col_2 align_center">
                    <!-- image upload -->
                    <div class="file_select profile_hold m-b margin_center">
                        <input type="file" id="file" name="image_url" class="upload_profile">
                        <img src="{{ asset($profile_user_details['image']) }}" class="target">
                    </div>

                    <p class="color_base weight_medium">Upload Photo</p>
                </div>

                <!-- edit profle fields -->
                <div class="column col_10 padd">
                    <h3 class="weight_medium mb">Basic Info</h3>

                    <div class="clearfix">
                        <div class="input_wrap column col_6">
                            <label class="small_faint uppercased weight_medium">First Name</label>
                            <input type="text" type="text" required id="first_name"
                                   name="first_name" placeholder="@lang('app.first_name')" value="{{ $profile_user_details['first_name'] }}" >
                        </div>

                        <div class="input_wrap column col_6">
                            <label class="small_faint uppercased weight_medium">Last Name</label>
                            <input type="text" required id="last_name"
                                   name="last_name" placeholder="@lang('app.last_name')" value="{{ $profile_user_details['last_name'] }}" >
                        </div>
                    </div>

                    <div class="clearfix">
                        <div class="input_wrap column col_6">
                            <label class="small_faint uppercased weight_medium">Email</label>
                            <input type="email" name="email" required value="{{ $profile_user_details['email'] }}" readonly placeholder="me@example.com">
                        </div>

                        <div class="input_wrap column col_6">
                            <label class="small_faint uppercased weight_medium">Mobile Number</label>
                            <input type="text" required name="phone" value="{{ $profile_user_details['phone'] }}" placeholder="+234** **** ****">
                        </div>
                    </div>

                    <div class="clearfix">
                        <div class="input_wrap column col_6">
                            <label class="small_faint uppercased weight_medium">Agency</label>
                            <input type="text" type="text" required id="username"
                                   name="username" placeholder="@lang('app.username')" value="{{ $profile_user_details['username'] }}" >
                        </div>

                        <div class="input_wrap column col_6">
                            <label class="small_faint uppercased weight_medium">Location</label>
                            <input type="text" required id="location"
                                   name="location" placeholder="@lang('app.location')" value="{{ $profile_user_details['location'] }}" >
                        </div>
                    </div>

                    <div class="clearfix mb4">
                        <div class="input_wrap column col_6">
                            <label class="small_faint uppercased weight_medium">Address</label>
                            <input type="text" required name="address" value="{{ $profile_user_details['address'] }}" placeholder="Address">
                        </div>

                        <div class="input_wrap column col_6">
                            <label class="small_faint uppercased weight_medium">Nationality</label>
                            <select name="country_id" required >
                                @foreach ($countries as $country)
                                    <option value="{{ $country->country_code }}"
                                            @if($profile_user_details['nationality'] === $country->country_code)
                                            selected
                                            @endif
                                    >{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                    <h3 class="weight_medium">Password</h3>
                    <p class="small_font mb ital light_font">Leave blank if you do not want to change.</p>

                    <div class="clearfix mb4">
                        <div class="input_wrap column col_6">
                            <label class="small_faint uppercased weight_medium">Password</label>
                            <input type="password" name="password" placeholder="****">
                        </div>

                        <div class="input_wrap column col_6">
                            <label class="small_faint uppercased weight_medium">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" placeholder="****">
                        </div>
                    </div>


                    <div class="mb4 align_right">
                        <input type="submit" value="Save Profile" class="btn uppercased mb4">
                    </div>

                </div>


                <!-- end -->

            </div>
        </form>

    </div>
</div>
