<div class="modal-header" style="border-bottom: 0">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="text-center">
                <img src="{{asset('build/images/logo-fixed-2.png')}}" style="width: 30%;">
            </h3>
            <h2 class="text-center">
                Welcome!
            </h2>
            <p class="text-center">
                We want you to get the most out of SpotLite. To kick-off we want to personalise your experience.
            </p>
            <p class="text-center">
                Samply answer the questions and follow the journey. Enjoy!
            </p>

        </div>
    </div>
    <div class="row steps-container">
        <div class="col-sm-12">
            <ul class="steps">
                <li data-step="1" class="active">
                    <span class="step">
                        <i class="fa fa-check"></i>
                    </span>
                    <div class="title">
                        <div>
                            STEP 1
                        </div>
                        <div class="description">
                            Let's kick off by creating your Dashboard
                        </div>
                    </div>
                </li>
                <li data-step="2">
                    <span class="step">&nbsp;</span>
                    <div class="title">
                        <div>
                            STEP 2
                        </div>
                        <div class="description">
                            Set up product prices you want to track
                        </div>
                    </div>
                </li>
                <li data-step="3">
                    <span class="step">&nbsp;</span>
                    <div class="title">
                        <div>
                            STEP 3
                        </div>
                        <div class="description">
                            Set notifications
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12 ">
            <div class="jumbotron set-password-form-container">
                <p class="welcome-form-heading">
                    <strong>STEP 1:</strong> Let's start by looking at what industry and company type you want to track:
                </p>

                <ul class="text-danger errors-container" id="welcome-errors-container">
                </ul>
                <form action="{{route('profile.init_update')}}" id="init-update-form"
                      onsubmit="submitInitUpdate(); return false; ">
                    <div class="form-group required">
                        <select class="form-control" name="industry">
                            <option value="">What is your industry?</option>
                            <option value="Accommodations">Accommodations</option>
                            <option value="Accounting">Accounting</option>
                            <option value="Advertising">Advertising</option>
                            <option value="Aerospace">Aerospace</option>
                            <option value="Agriculture & Agribusiness">Agriculture & Agribusiness</option>
                            <option value="Air Transportation">Air Transportation</option>
                            <option value="Apparel & Accessories">Apparel & Accessories</option>
                            <option value="Auto">Auto</option>
                            <option value="Banking">Banking</option>
                            <option value="Beauty & Cosmetics">Beauty & Cosmetics</option>
                            <option value="Biotechnology">Biotechnology</option>
                            <option value="Chemical">Chemical</option>
                            <option value="Communications">Communications</option>
                            <option value="Computer">Computer</option>
                            <option value="Construction">Construction</option>
                            <option value="Consulting">Consulting</option>
                            <option value="Consumer Products">Consumer Products</option>
                            <option value="Education">Education</option>
                            <option value="Electronics">Electronics</option>
                            <option value="Employment">Employment</option>
                            <option value="Energy">Energy</option>
                            <option value="Entertainment & Recreation">Entertainment & Recreation</option>
                            <option value="Fashion">Fashion</option>
                            <option value="Financial Services">Financial Services</option>
                            <option value="Fine Arts">Fine Arts</option>
                            <option value="Food & Beverage">Food & Beverage</option>
                            <option value="Health">Health</option>
                            <option value="Information">Information</option>
                            <option value="Information Technology">Information Technology</option>
                            <option value="Insurance">Insurance</option>
                            <option value="Journalism & News">Journalism & News</option>
                            <option value="Legal Services">Legal Services</option>
                            <option value="Manufacturing">Manufacturing</option>
                            <option value="Media & Broadcasting">Media & Broadcasting</option>
                            <option value="Medical Devices & Supplies">Medical Devices & Supplies</option>
                            <option value="Motion Pictures & Video">Motion Pictures & Video</option>
                            <option value="Music">Music</option>
                            <option value="Pharmaceutical">Pharmaceutical</option>
                            <option value="Public Administration">Public Administration</option>
                            <option value="Public Relations">Public Relations</option>
                            <option value="Publishing">Publishing</option>
                            <option value="Real Estate">Real Estate</option>
                            <option value="Retail">Retail</option>
                            <option value="Service">Service</option>
                            <option value="Sports">Sports</option>
                            <option value="Technology">Technology</option>
                            <option value="Telecommunications">Telecommunications</option>
                            <option value="Tourism">Tourism</option>
                            <option value="Transportation">Transportation</option>
                            <option value="Travel">Travel</option>
                            <option value="Utilities">Utilities</option>
                            <option value="Video Game">Video Game</option>
                            <option value="Web Services">Web Services</option>
                        </select>
                    </div>
                    <div class="form-group required">
                        <select name="company_type" class="form-control">
                            <option value="">What Company Type?</option>
                            <option value="Retailer">Retailer</option>
                            <option value="Brand">Brand</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <select name="category" id="" class="form-control">
                            <option value="">
                                Would you like us to set up some sample data for you?
                            </option>
                            @if(isset($sampleData))
                                @foreach($sampleData as $item)
                                    <option value="{{$item}}">{{$item}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="text" name="company_url" class="form-control"
                               placeholder="Company URL? e.g. http://www.example.com">
                    </div>
                    <div class="form-group text-center">
                        <button class="btn btn-primary btn-flat" type="submit">Create Dashboard</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        function submitInitUpdate() {
            cleanErrorMessage();
            showLoading();
            $.ajax({
                "url": $("#init-update-form").attr("action"),
                "method": "put",
                "data": $("#init-update-form").serialize(),
                "success": function (response) {
                    hideLoading();
                    if (response.status == true) {
                        showLoading();
                        window.location.href = "{{route('dashboard.index')}}";
                    } else {
                        if (typeof response.errors != 'undefined') {
                            var $errorContainer = $("#welcome-errors-container");
                            $errorContainer.empty();
                            $.each(response.errors, function (index, error) {
                                $errorContainer.append(
                                        $("<li>").text(error)
                                );
                            });
                        } else {
                            alertP("Error", "Unable to perform initial set up, please try again later.");
                        }
                    }
                },
                "error": function (xhr, status, error) {
                    hideLoading();
                    describeServerRespondedError(xhr.status);
                }
            });
        }

        function cleanErrorMessage() {
            $("#welcome-errors-container").empty();
        }
    </script>
</div>