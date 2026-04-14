@extends('layouts.master')

@section('content')


<!-- PAGE -->
<div class="page">
    <div class="page-main">





        <!--app-content open-->
        <div class="main-content app-content mt-0">
            <div class="side-app">

                <!-- CONTAINER -->
                <div class="main-container container-fluid">

                    <!-- PAGE-HEADER -->
                    <div class="page-header">
                        <h1 class="page-title">Switcher Style-1</h1>
                        <div>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="javascript:void(0)">Switcher</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Switcher Style-1</li>
                            </ol>
                        </div>
                    </div>
                    <!-- PAGE-HEADER END -->

                    <!--Row-->
                    <div class="container">
                        <div class="row row-sm">
                            <div class="col-xl-6 m-auto">
                                <div class="card sidebar-right1">
                                    <div class="card-body">
                                        <div>
                                            <h6 class="main-content-label mb-3">Navigation Style</h6>
                                        </div>
                                        <div class="switch_section">
                                            <div class="switch-toggle d-flex">
                                                <span class="me-auto">Vertical Menu</span>
                                                <p class="onoffswitch2"><input type="radio" name="onoffswitch15"
                                                        id="myonoffswitch34" class="onoffswitch2-checkbox" checked>
                                                    <label for="myonoffswitch34" class="onoffswitch2-label"></label>
                                                </p>
                                            </div>
                                            <div class="switch-toggle d-flex mt-2">
                                                <span class="me-auto">Horizontal Click Menu</span>
                                                <p class="onoffswitch2"><input type="radio" name="onoffswitch15"
                                                        id="myonoffswitch35" class="onoffswitch2-checkbox">
                                                    <label for="myonoffswitch35" class="onoffswitch2-label"></label>
                                                </p>
                                            </div>
                                            <div class="switch-toggle d-flex mt-2">
                                                <span class="me-auto">Horizontal Hover Menu</span>
                                                <p class="onoffswitch2"><input type="radio" name="onoffswitch15"
                                                        id="myonoffswitch111" class="onoffswitch2-checkbox">
                                                    <label for="myonoffswitch111" class="onoffswitch2-label"></label>
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <div>
                                            <h6 class="main-content-label mb-3">Theme Style</h6>
                                        </div>
                                        <div class="switch_section">
                                            <div class="switch-toggle d-flex">
                                                <span class="me-auto">Light Theme</span>
                                                <p class="onoffswitch2"><input type="radio" name="onoffswitch1"
                                                        id="myonoffswitch1" class="onoffswitch2-checkbox" checked>
                                                    <label for="myonoffswitch1" class="onoffswitch2-label"></label>
                                                </p>
                                            </div>
                                            <div class="switch-toggle d-flex ">
                                                <span class="me-auto">Dark Theme</span>
                                                <p class="onoffswitch2"><input type="radio" name="onoffswitch1"
                                                        id="myonoffswitch2" class="onoffswitch2-checkbox">
                                                    <label for="myonoffswitch2" class="onoffswitch2-label"></label>
                                                </p>
                                            </div>
                                            <div class="switch-toggle d-flex">
                                                <span class="me-auto">Light Primary</span>
                                                <div class="">
                                                    <input class="w-30p h-30 input-color-picker color-primary-light"
                                                        value="#6c5ffc" id="colorID" oninput="changePrimaryColor()"
                                                        type="color" data-id="bg-color" data-id1="bg-hover"
                                                        data-id2="bg-border" data-id7="transparentcolor"
                                                        name="lightPrimary">
                                                </div>
                                            </div>


                                            <div class="switch-toggle d-flex ">
                                                <span class="me-auto">Dark Primary</span>
                                                <div class="">
                                                    <input class="w-30p h-30 input-dark-color-picker color-primary-dark"
                                                        value="#6c5ffc" id="darkPrimaryColorID"
                                                        oninput="darkPrimaryColor()" type="color" data-id="bg-color"
                                                        data-id1="bg-hover" data-id2="bg-border" data-id3="primary"
                                                        data-id8="transparentcolor" name="darkPrimary">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                   
                                  
                                    <div class="card-body">
                                        <div>
                                            <h6 class="main-content-label mb-3">Leftmenu Styles</h6>
                                        </div>
                                        <div class="switch_section">
                                            <div class="switch-toggle lightMenu d-flex">
                                                <span class="me-auto">Light Menu</span>
                                                <p class="onoffswitch2"><input type="radio" name="onoffswitch2"
                                                        id="myonoffswitch3" class="onoffswitch2-checkbox" checked>
                                                    <label for="myonoffswitch3" class="onoffswitch2-label"></label>
                                                </p>
                                            </div>
                                            <div class="switch-toggle colorMenu d-flex mt-2">
                                                <span class="me-auto">Color Menu</span>
                                                <p class="onoffswitch2"><input type="radio" name="onoffswitch2"
                                                        id="myonoffswitch4" class="onoffswitch2-checkbox">
                                                    <label for="myonoffswitch4" class="onoffswitch2-label"></label>
                                                </p>
                                            </div>
                                            <div class="switch-toggle darkMenu d-flex mt-2">
                                                <span class="me-auto">Dark Menu</span>
                                                <p class="onoffswitch2"><input type="radio" name="onoffswitch2"
                                                        id="myonoffswitch5" class="onoffswitch2-checkbox">
                                                    <label for="myonoffswitch5" class="onoffswitch2-label"></label>
                                                </p>
                                            </div>
                                            <div class="switch-toggle gradientMenu d-flex mt-2">
                                                <span class="me-auto">Gradient Menu</span>
                                                <p class="onoffswitch2"><input type="radio" name="onoffswitch2"
                                                        id="myonoffswitch19" class="onoffswitch2-checkbox">
                                                    <label for="myonoffswitch19" class="onoffswitch2-label"></label>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div>
                                            <h6 class="main-content-label mb-3">Header Styles</h6>
                                        </div>
                                        <div class="switch_section">
                                            <div class="switch-toggle lightHeader d-flex">
                                                <span class="me-auto">Light Header</span>
                                                <p class="onoffswitch2"><input type="radio" name="onoffswitch3"
                                                        id="myonoffswitch6" class="onoffswitch2-checkbox" checked>
                                                    <label for="myonoffswitch6" class="onoffswitch2-label"></label>
                                                </p>
                                            </div>
                                            <div class="switch-toggle  colorHeader d-flex mt-2">
                                                <span class="me-auto">Color Header</span>
                                                <p class="onoffswitch2"><input type="radio" name="onoffswitch3"
                                                        id="myonoffswitch7" class="onoffswitch2-checkbox">
                                                    <label for="myonoffswitch7" class="onoffswitch2-label"></label>
                                                </p>
                                            </div>
                                            <div class="switch-toggle darkHeader d-flex mt-2">
                                                <span class="me-auto">Dark Header</span>
                                                <p class="onoffswitch2"><input type="radio" name="onoffswitch3"
                                                        id="myonoffswitch8" class="onoffswitch2-checkbox">
                                                    <label for="myonoffswitch8" class="onoffswitch2-label"></label>
                                                </p>
                                            </div>

                                            <div class="switch-toggle darkHeader d-flex mt-2">
                                                <span class="me-auto">Gradient Header</span>
                                                <p class="onoffswitch2"><input type="radio" name="onoffswitch3"
                                                        id="myonoffswitch20" class="onoffswitch2-checkbox">
                                                    <label for="myonoffswitch20" class="onoffswitch2-label"></label>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                   
                                  
                                    <div class="card-body leftmenu-styles">
                                        <div>
                                            <h6 class="main-content-label mb-3">Sidemenu Layout Styles</h6>
                                        </div>
                                        <div class="switch_section">
                                            <div class="switch-toggle d-flex">
                                                <span class="me-auto">Default Menu</span>
                                                <p class="onoffswitch2"><input type="radio" name="onoffswitch6"
                                                        id="myonoffswitch13" class="onoffswitch2-checkbox default-menu"
                                                        checked>
                                                    <label for="myonoffswitch13" class="onoffswitch2-label"></label>
                                                </p>
                                            </div>
                                            <div class="switch-toggle d-flex mt-2">
                                                <span class="me-auto">Icon with Text</span>
                                                <p class="onoffswitch2"><input type="radio" name="onoffswitch6"
                                                        id="myonoffswitch14" class="onoffswitch2-checkbox">
                                                    <label for="myonoffswitch14" class="onoffswitch2-label"></label>
                                                </p>
                                            </div>
                                            <div class="switch-toggle d-flex mt-2">
                                                <span class="me-auto">Icon Overlay</span>
                                                <p class="onoffswitch2"><input type="radio" name="onoffswitch6"
                                                        id="myonoffswitch15" class="onoffswitch2-checkbox">
                                                    <label for="myonoffswitch15" class="onoffswitch2-label"></label>
                                                </p>
                                            </div>
                                            <div class="switch-toggle d-flex mt-2">
                                                <span class="me-auto">Closed Sidemenu</span>
                                                <p class="onoffswitch2"><input type="radio" name="onoffswitch6"
                                                        id="myonoffswitch16" class="onoffswitch2-checkbox">
                                                    <label for="myonoffswitch16" class="onoffswitch2-label"></label>
                                                </p>
                                            </div>
                                            <div class="switch-toggle d-flex mt-2">
                                                <span class="me-auto">Hover Submenu</span>
                                                <p class="onoffswitch2"><input type="radio" name="onoffswitch6"
                                                        id="myonoffswitch17" class="onoffswitch2-checkbox">
                                                    <label for="myonoffswitch17" class="onoffswitch2-label"></label>
                                                </p>
                                            </div>
                                            <div class="switch-toggle d-flex mt-2">
                                                <span class="me-auto">Hover Submenu Style 1</span>
                                                <p class="onoffswitch2"><input type="radio" name="onoffswitch6"
                                                        id="myonoffswitch18" class="onoffswitch2-checkbox">
                                                    <label for="myonoffswitch18" class="onoffswitch2-label"></label>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="switch_section text-center px-0">
                                            <div class="btn-list">
                                                <button class="btn btn-success w-lg">Save Settings</button>
                                                <button class="btn btn-danger" onclick="localStorage.clear();
                                                    document.querySelector('html').style = '';
                                                    names() ;
                                                    resetData() ;" type="button">Reset All
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--End Row-->
                </div>
                <!-- CONTAINER CLOSED -->
            </div>
        </div>
        <!--app-content closed-->
    </div>





</div>



@section('scripts')

@endsection

@endsection