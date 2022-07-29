<?php
    $socialList = getSocialLink();
    $menusFooter = getMenuContent('Footer');
    $logo = getCompanyLogoWithoutSession(); //direct query
?>

<style> 
    .mybtn-sub{
        background: #009fff;
        border-radius: 5px;
        font-size: 12px;
        color: white;
        border:  none;
        padding: 0 10px;
        height: 50px;
    }

</style>    

<section style=" background: #0051ff; color: white;">   
    <div class="container" style="padding: 30px;"> 
        <div class="row">
            <div class="col-md-6 mb-4">
                <h5>SUBSCRIBE NEWSLETTER</h5>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit.uos quidem corporis adipisci commodi. </p>
            </div> 
            <div class="col-md-6" style="display: flex;">
                <input class="form-control" placeholder="Enter your email here" type=" ">
                <button type="button" class="mybtn-sub" value="SUBSCRIBE">SUBSCRIBE</button>
            </div> 
        </div>  
    </div>

</section>  
<section class="bg-image footer text-white">
    <div class="" style="background: #0e0d1b">
        <div class="container pt-60 pb-3">
            <div class="row  justify-content-between">
                
                <div class="col-md-5">
                    <div>
                        <img src="{{theme_asset('public/images/logos/'.$logo)}}" class="mw-200" alt="logo">
                    </div>
                    <p class="mt-3">Ashpath is a company established to offer safe payment solutions and logistic services to business in Nigeria. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Vitae eaque veritatis praesentium deleniti! Repudiandae nobis consectetur.</p>
                </div>

                <div class="col-md-2 pt-3">
                    <div class="d-flex flex-wrap justify-content-between">
                        <div>
                            <h4>{{ __('Quick Links') }}</h4>
                            <ul class="link mt-3">
                                <li class="mt-2"><a href="{{ url('/') }}" class="text-white">FAQ</a></li>
                                <li class="mt-2"><a href="#" class="text-white">Contact</a></li>
                                <li class="mt-2"><a href="{{ url('/request-money') }}" class="text-white">About</a></li>
                                <li class="mt-2"><a href="{{ url('/developer') }}" class="text-white">Services</a></li>
                            </ul>
                        </div>

                        <div>
                            <ul class="link mt-4 pt-1">
                                @if(!empty($menusFooter))
                                    @foreach($menusFooter as $footer_navbar)
                                        <li class="mt-2"><a href="{{url($footer_navbar->url)}}" class="text-white">{{ $footer_navbar->name }}</a></li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-2 pt-3">
                    <div class="d-flex flex-wrap justify-content-between">
                        <div>
                            <h4>{{ __('Quick Links') }}</h4>
                            <ul class="link mt-3">
                                <li class="mt-2"><a href="{{ url('/') }}" class="text-white">FAQ</a></li>
                                <li class="mt-2"><a href="#" class="text-white">Contact</a></li>
                                <li class="mt-2"><a href="{{ url('/request-money') }}" class="text-white">About</a></li>
                                <li class="mt-2"><a href="{{ url('/developer') }}" class="text-white">Services</a></li>
                            </ul>
                        </div>

                        <div>
                            <ul class="link mt-4 pt-1">
                                @if(!empty($menusFooter))
                                    @foreach($menusFooter as $footer_navbar)
                                        <li class="mt-2"><a href="{{url($footer_navbar->url)}}" class="text-white">{{ $footer_navbar->name }}</a></li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>


                <div class="col-md-3 pt-3">
                    <div class="d-flex flex-wrap justify-content-between">
                        <div>
                            <h4>{{ __('Quick Links') }}</h4>
                            <ul class="link mt-3">
                                <li class="mt-2"><a href="{{ url('/') }}" class="text-white">FAQ</a></li>
                                <li class="mt-2"><a href="#" class="text-white">Contact</a></li>
                                <li class="mt-2"><a href="{{ url('/request-money') }}" class="text-white">About</a></li>
                                <li class="mt-2"><a href="{{ url('/developer') }}" class="text-white">Services</a></li>
                            </ul>
                        </div>

                        <div>
                            <ul class="link mt-4 pt-1">
                                @if(!empty($menusFooter))
                                    @foreach($menusFooter as $footer_navbar)
                                        <li class="mt-2"><a href="{{url($footer_navbar->url)}}" class="text-white">{{ $footer_navbar->name }}</a></li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>


            </div>

            <div class="d-flex flex-wrap justify-content-between">
                <div>
                    <div class="d-flex justify-content-center">
                        <div class="d-flex flex-wrap social-icons mt-5">
                            @foreach($socialList as $social)
                                @if (!empty($social->url))
                                    <div class="p-2">
                                        <a href="{{ $social->url }}">{!! $social->icon !!}</a>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <div>
                    <div class="d-flex justify-content-center pt-4">

                        @foreach(getAppStoreLinkFrontEnd() as $app)
                            @if (!empty($app->logo))
                                <div class="p-2 pl-4 pr-4">
                                    <a href="{{$app->link}}"><img src="{{url('public/uploads/app-store-logos/'.$app->logo)}}" class="img-responsive" width="125" height="50"/></a>
                                </div>
                            @else
                                <div class="p-2 pl-4 pr-4">
                                    <a href="#"><img src='{{ url('public/uploads/app-store-logos/default-logo.jpg') }}' class="img-responsive" width="120" height="90"/></a>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <hr class="mb-2">
            <div class="d-flex justify-content-between">
                <div>
                    <?php
                        $company_name = 'Ashpath';
                    ?>
                    <p class="copyright mt-0">@lang('message.footer.copyright')&nbsp;© {{date('Y')}} &nbsp;&nbsp; {{ $company_name }} | @lang('message.footer.copyright-text')</p>
                </div>

                <div>
                    <div class="container-select d-flex">
                        <div>
                            <i class="fa fa-globe"></i>
                        </div>

                        <div>
                            <select class="select-custom mt-0" id="lang">
                                @foreach (getLanguagesListAtFooterFrontEnd() as $lang)
                                <option {{ Session::get('dflt_lang') == $lang->short_name ? 'selected' : '' }} value='{{ $lang->short_name }}'> {{ $lang->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</section>
