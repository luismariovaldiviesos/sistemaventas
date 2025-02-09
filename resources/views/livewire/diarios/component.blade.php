<!DOCTYPE html>
<!--
Template Name: Rubick - HTML Admin Dashboard Template
Author: Left4code
Website: http://www.left4code.com/
Contact: muhammadrizki@left4code.com
Purchase: https://themeforest.net/user/left4code/portfolio
Renew Support: https://themeforest.net/user/left4code/portfolio
License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.
-->
<html lang="en" class="light">

    <!-- END: Head -->
    <body class="main">


        <div class="flex">


            <div class="content">

                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-12 xxl:col-span-9">
                        <div class="grid grid-cols-12 gap-6">
                            <!-- BEGIN: Notification -->

                            <!-- BEGIN: Notification -->
                            <!-- BEGIN: General Report -->
                            <div class="col-span-12 lg:col-span-8 xl:col-span-6 mt-2">
                                <div class="intro-y block sm:flex items-center h-10">
                                    <h2 class="text-lg font-medium truncate mr-5">
                                        REPORTE DIARIO {{ $day }}
                                    </h2>

                                </div>
                                <div class="report-box-2 intro-y mt-12 sm:mt-5">
                                    <div class="box sm:flex">
                                        <div class="px-8 py-12 flex flex-col justify-center flex-1">
                                            <i data-feather="shopping-bag" class="w-10 h-10 text-theme-12"></i>
                                            <div class="relative text-5xl font-bold mt-12 pl-4"> <span class="absolute text-xl top-0 left-0">$</span>  {{ $totVentas }}</div>

                                            <div class="flex items-center sm:ml-auto mt-3 sm:mt-0">
                                                    {{-- <button wire:click.prevent="ReportPDF"  class="btn btn-danger " >
                                                        <i data-feather="file-text" class=" hidden sm:block w-4 h-4 mr-2">
                                                        </i> PDF
                                                    </button> --}}
                                                    {{-- <a class="btn btn-danger btn-block"
                                                    href="{{ url('report/pdf') }}" target="_blank">
                                                    <i data-feather="file-text" class=" hidden sm:block w-4 h-4 mr-2">
                                                    </i> GENERAR PDF --}}
                                                </a>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <!-- END: General Report -->
                            <!-- BEGIN: Visitors -->
                            <div class="col-span-12 sm:col-span-6 lg:col-span-4 xl:col-span-3 mt-2">

                                <div class="report-box-2 intro-y mt-5">
                                    <div class="box p-5">
                                        <div class="flex items-center">
                                            Clientes registrados:
                                            <div class="dropdown ml-auto">
                                                <a class="dropdown-toggle w-5 h-5 block -mr-2" href="javascript:;" aria-expanded="false"> <i data-feather="more-vertical" class="w-5 h-5 text-gray-600 dark:text-gray-300"></i> </a>
                                                <div class="dropdown-menu w-40">
                                                    {{-- <div class="dropdown-menu__content box dark:bg-dark-1 p-2">
                                                        <a href="" class="flex items-center block p-2 transition duration-300 ease-in-out bg-white dark:bg-dark-1 hover:bg-gray-200 dark:hover:bg-dark-2 rounded-md"> <i data-feather="file-text" class="w-4 h-4 mr-2"></i> Export </a>
                                                        <a href="" class="flex items-center block p-2 transition duration-300 ease-in-out bg-white dark:bg-dark-1 hover:bg-gray-200 dark:hover:bg-dark-2 rounded-md"> <i data-feather="settings" class="w-4 h-4 mr-2"></i> Settings </a>
                                                    </div> --}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-2xl font-medium mt-2">{{ $clientes }}</div>

                                        <div class="mt-2 border-b broder-gray-200">
                                            <div class="-mb-1.5 -ml-2.5">
                                                <canvas id="report-bar-chart" height="111"></canvas>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>
                            <!-- END: Visitors -->
                            <!-- BEGIN: Users By Age -->
                            <div class="col-span-12 sm:col-span-6 lg:col-span-4 xl:col-span-3 mt-2 lg:mt-6 xl:mt-2">
                                <div class="intro-y flex items-center h-10">
                                    <h2 class="text-lg font-medium truncate mr-5">
                                       FACTURAS DE HOY
                                    </h2>

                                </div>
                                <div class="report-box-2 intro-y mt-5">
                                    <div class="box p-5">

                                        <div class="tab-content mt-6">
                                            <div class="tab-pane active" id="active-users" role="tabpanel" aria-labelledby="active-users-tab">
                                                <div class="relative">
                                                    <canvas class="mt-3" id="report-donut-chart" height="300"></canvas>
                                                    <div class="flex flex-col justify-center items-center absolute w-full h-full top-0 left-0">
                                                        <div class="text-5xl font-medium">{{ $numVentas }}</div>
                                                        <div class="text-gray-600 dark:text-gray-600 mt-0.5">ventas registradas</div>
                                                    </div>
                                                </div>
                                                <div class="mt-5">
                                                    <div class="flex items-center">
                                                        <div class="w-2 h-2 bg-theme-11 rounded-full mr-3"></div>
                                                        <span class="truncate">17 - 30 Years old</span>
                                                        <div class="h-px flex-1 border border-r border-dashed border-gray-300 mx-3 xl:hidden"></div>
                                                        <span class="font-medium xl:ml-auto">62%</span>
                                                    </div>
                                                    <div class="flex items-center mt-4">
                                                        <div class="w-2 h-2 bg-theme-1 rounded-full mr-3"></div>
                                                        <span class="truncate">31 - 50 Years old</span>
                                                        <div class="h-px flex-1 border border-r border-dashed border-gray-300 mx-3 xl:hidden"></div>
                                                        <span class="font-medium xl:ml-auto">33%</span>
                                                    </div>
                                                    <div class="flex items-center mt-4">
                                                        <div class="w-2 h-2 bg-theme-12 rounded-full mr-3"></div>
                                                        <span class="truncate">>= 50 Years old</span>
                                                        <div class="h-px flex-1 border border-r border-dashed border-gray-300 mx-3 xl:hidden"></div>
                                                        <span class="font-medium xl:ml-auto">10%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-span-12 xxl:col-span-3">
                        <div class="xxl:border-l border-theme-5 -mb-10 pb-10">
                            <div class="xxl:pl-6 grid grid-cols-12 gap-6">
                                <!-- BEGIN: Important Notes -->
                                <div class="col-span-12 md:col-span-6 xl:col-span-12 mt-3 xxl:mt-8">
                                    <div class="intro-x flex items-center h-10">
                                        <h2 class="text-lg font-medium truncate mr-auto">
                                           TOMAR EN CUENTA
                                        </h2>
                                        <button data-carousel="important-notes" data-target="prev" class="tiny-slider-navigator btn px-2 border-gray-400 text-gray-700 dark:text-gray-300 mr-2"> <i data-feather="chevron-left" class="w-4 h-4"></i> </button>
                                        <button data-carousel="important-notes" data-target="next" class="tiny-slider-navigator btn px-2 border-gray-400 text-gray-700 dark:text-gray-300 mr-2"> <i data-feather="chevron-right" class="w-4 h-4"></i> </button>
                                    </div>
                                    <div class="mt-5 intro-x">
                                        <div class="box zoom-in">
                                            <div class="tiny-slider" id="important-notes">
                                                <div class="p-5">
                                                    <div class="text-base font-medium truncate">Lorem Ipsum is simply dummy text</div>
                                                    <div class="text-gray-500 mt-1">20 Hours ago</div>
                                                    <div class="text-gray-600 text-justify mt-1">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.</div>
                                                    <div class="font-medium flex mt-5">
                                                        <button type="button" class="btn btn-secondary py-1 px-2">View Notes</button>
                                                        <button type="button" class="btn btn-outline-secondary py-1 px-2 ml-auto ml-auto">Dismiss</button>
                                                    </div>
                                                </div>
                                                <div class="p-5">
                                                    <div class="text-base font-medium truncate">Lorem Ipsum is simply dummy text</div>
                                                    <div class="text-gray-500 mt-1">20 Hours ago</div>
                                                    <div class="text-gray-600 text-justify mt-1">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.</div>
                                                    <div class="font-medium flex mt-5">
                                                        <button type="button" class="btn btn-secondary py-1 px-2">View Notes</button>
                                                        <button type="button" class="btn btn-outline-secondary py-1 px-2 ml-auto ml-auto">Dismiss</button>
                                                    </div>
                                                </div>
                                                <div class="p-5">
                                                    <div class="text-base font-medium truncate">Lorem Ipsum is simply dummy text</div>
                                                    <div class="text-gray-500 mt-1">20 Hours ago</div>
                                                    <div class="text-gray-600 text-justify mt-1">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.</div>
                                                    <div class="font-medium flex mt-5">
                                                        <button type="button" class="btn btn-secondary py-1 px-2">View Notes</button>
                                                        <button type="button" class="btn btn-outline-secondary py-1 px-2 ml-auto ml-auto">Dismiss</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- END: Important Notes -->
                                <!-- BEGIN: Recent Activities -->
                                {{-- <div class="col-span-12 md:col-span-6 xl:col-span-4 xxl:col-span-12 mt-3">
                                    <div class="intro-x flex items-center h-10">
                                        <h2 class="text-lg font-medium truncate mr-5">
                                            Recent Activities
                                        </h2>
                                        <a href="" class="ml-auto text-theme-1 dark:text-theme-10 truncate">Show More</a>
                                    </div>
                                    <div class="report-timeline mt-5 relative">
                                        <div class="intro-x relative flex items-center mb-3">
                                            <div class="report-timeline__image">
                                                <div class="w-10 h-10 flex-none image-fit rounded-full overflow-hidden">
                                                    <img alt="Rubick Tailwind HTML Admin Template" src="dist/images/profile-1.jpg">
                                                </div>
                                            </div>
                                            <div class="box px-5 py-3 ml-4 flex-1 zoom-in">
                                                <div class="flex items-center">
                                                    <div class="font-medium">Robert De Niro</div>
                                                    <div class="text-xs text-gray-500 ml-auto">07:00 PM</div>
                                                </div>
                                                <div class="text-gray-600 mt-1">Has joined the team</div>
                                            </div>
                                        </div>
                                        <div class="intro-x relative flex items-center mb-3">
                                            <div class="report-timeline__image">
                                                <div class="w-10 h-10 flex-none image-fit rounded-full overflow-hidden">
                                                    <img alt="Rubick Tailwind HTML Admin Template" src="dist/images/profile-6.jpg">
                                                </div>
                                            </div>
                                            <div class="box px-5 py-3 ml-4 flex-1 zoom-in">
                                                <div class="flex items-center">
                                                    <div class="font-medium">Al Pacino</div>
                                                    <div class="text-xs text-gray-500 ml-auto">07:00 PM</div>
                                                </div>
                                                <div class="text-gray-600">
                                                    <div class="mt-1">Added 3 new photos</div>
                                                    <div class="flex mt-2">
                                                        <div class="tooltip w-8 h-8 image-fit mr-1 zoom-in" title="Sony Master Series A9G">
                                                            <img alt="Rubick Tailwind HTML Admin Template" class="rounded-md border border-white" src="dist/images/preview-15.jpg">
                                                        </div>
                                                        <div class="tooltip w-8 h-8 image-fit mr-1 zoom-in" title="Nike Tanjun">
                                                            <img alt="Rubick Tailwind HTML Admin Template" class="rounded-md border border-white" src="dist/images/preview-14.jpg">
                                                        </div>
                                                        <div class="tooltip w-8 h-8 image-fit mr-1 zoom-in" title="Sony Master Series A9G">
                                                            <img alt="Rubick Tailwind HTML Admin Template" class="rounded-md border border-white" src="dist/images/preview-13.jpg">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="intro-x text-gray-500 text-xs text-center my-4">12 November</div>
                                        <div class="intro-x relative flex items-center mb-3">
                                            <div class="report-timeline__image">
                                                <div class="w-10 h-10 flex-none image-fit rounded-full overflow-hidden">
                                                    <img alt="Rubick Tailwind HTML Admin Template" src="dist/images/profile-4.jpg">
                                                </div>
                                            </div>
                                            <div class="box px-5 py-3 ml-4 flex-1 zoom-in">
                                                <div class="flex items-center">
                                                    <div class="font-medium">John Travolta</div>
                                                    <div class="text-xs text-gray-500 ml-auto">07:00 PM</div>
                                                </div>
                                                <div class="text-gray-600 mt-1">Has changed <a class="text-theme-1 dark:text-theme-10" href="">Samsung Galaxy S20 Ultra</a> price and description</div>
                                            </div>
                                        </div>
                                        <div class="intro-x relative flex items-center mb-3">
                                            <div class="report-timeline__image">
                                                <div class="w-10 h-10 flex-none image-fit rounded-full overflow-hidden">
                                                    <img alt="Rubick Tailwind HTML Admin Template" src="dist/images/profile-5.jpg">
                                                </div>
                                            </div>
                                            <div class="box px-5 py-3 ml-4 flex-1 zoom-in">
                                                <div class="flex items-center">
                                                    <div class="font-medium">Johnny Depp</div>
                                                    <div class="text-xs text-gray-500 ml-auto">07:00 PM</div>
                                                </div>
                                                <div class="text-gray-600 mt-1">Has changed <a class="text-theme-1 dark:text-theme-10" href="">Sony A7 III</a> description</div>
                                            </div>
                                        </div>
                                    </div>
                                </div> --}}
                                <!-- END: Recent Activities -->
                                <!-- BEGIN: Transactions -->
                                {{-- <div class="col-span-12 md:col-span-6 xl:col-span-4 xxl:col-span-12 mt-3">
                                    <div class="intro-x flex items-center h-10">
                                        <h2 class="text-lg font-medium truncate mr-5">
                                            Transactions
                                        </h2>
                                    </div>
                                    <div class="mt-5">
                                        <div class="intro-x">
                                            <div class="box px-5 py-3 mb-3 flex items-center zoom-in">
                                                <div class="w-10 h-10 flex-none image-fit rounded-full overflow-hidden">
                                                    <img alt="Rubick Tailwind HTML Admin Template" src="dist/images/profile-8.jpg">
                                                </div>
                                                <div class="ml-4 mr-auto">
                                                    <div class="font-medium">Russell Crowe</div>
                                                    <div class="text-gray-600 text-xs mt-0.5">10 December 2022</div>
                                                </div>
                                                <div class="text-theme-9">+$200</div>
                                            </div>
                                        </div>
                                        <div class="intro-x">
                                            <div class="box px-5 py-3 mb-3 flex items-center zoom-in">
                                                <div class="w-10 h-10 flex-none image-fit rounded-full overflow-hidden">
                                                    <img alt="Rubick Tailwind HTML Admin Template" src="dist/images/profile-7.jpg">
                                                </div>
                                                <div class="ml-4 mr-auto">
                                                    <div class="font-medium">Denzel Washington</div>
                                                    <div class="text-gray-600 text-xs mt-0.5">9 June 2022</div>
                                                </div>
                                                <div class="text-theme-9">+$109</div>
                                            </div>
                                        </div>
                                        <div class="intro-x">
                                            <div class="box px-5 py-3 mb-3 flex items-center zoom-in">
                                                <div class="w-10 h-10 flex-none image-fit rounded-full overflow-hidden">
                                                    <img alt="Rubick Tailwind HTML Admin Template" src="dist/images/profile-12.jpg">
                                                </div>
                                                <div class="ml-4 mr-auto">
                                                    <div class="font-medium">Russell Crowe</div>
                                                    <div class="text-gray-600 text-xs mt-0.5">14 April 2021</div>
                                                </div>
                                                <div class="text-theme-9">+$51</div>
                                            </div>
                                        </div>
                                        <div class="intro-x">
                                            <div class="box px-5 py-3 mb-3 flex items-center zoom-in">
                                                <div class="w-10 h-10 flex-none image-fit rounded-full overflow-hidden">
                                                    <img alt="Rubick Tailwind HTML Admin Template" src="dist/images/profile-8.jpg">
                                                </div>
                                                <div class="ml-4 mr-auto">
                                                    <div class="font-medium">Edward Norton</div>
                                                    <div class="text-gray-600 text-xs mt-0.5">19 October 2021</div>
                                                </div>
                                                <div class="text-theme-9">+$96</div>
                                            </div>
                                        </div>
                                        <div class="intro-x">
                                            <div class="box px-5 py-3 mb-3 flex items-center zoom-in">
                                                <div class="w-10 h-10 flex-none image-fit rounded-full overflow-hidden">
                                                    <img alt="Rubick Tailwind HTML Admin Template" src="dist/images/profile-10.jpg">
                                                </div>
                                                <div class="ml-4 mr-auto">
                                                    <div class="font-medium">Tom Cruise</div>
                                                    <div class="text-gray-600 text-xs mt-0.5">20 October 2022</div>
                                                </div>
                                                <div class="text-theme-6">-$46</div>
                                            </div>
                                        </div>
                                        <a href="" class="intro-x w-full block text-center rounded-md py-3 border border-dotted border-theme-15 dark:border-dark-5 text-theme-16 dark:text-gray-600">View More</a>
                                    </div>
                                </div> --}}
                                <!-- END: Transactions -->
                                <!-- BEGIN: Schedules -->
                                {{-- <div class="col-span-12 md:col-span-6 xl:col-span-4 xxl:col-span-12 mt-3">
                                    <div class="intro-x flex items-center h-10">
                                        <h2 class="text-lg font-medium truncate mr-5">
                                            Schedules
                                        </h2>
                                        <a href="" class="ml-auto text-theme-1 dark:text-theme-10 truncate flex items-center"> <i data-feather="plus" class="w-4 h-4 mr-1"></i> Add New Schedules </a>
                                    </div>
                                    <div class="mt-5">
                                        <div class="intro-x box">
                                            <div class="p-5">
                                                <div class="flex">
                                                    <i data-feather="chevron-left" class="w-5 h-5 text-gray-600"></i>
                                                    <div class="font-medium text-base mx-auto">April</div>
                                                    <i data-feather="chevron-right" class="w-5 h-5 text-gray-600"></i>
                                                </div>
                                                <div class="grid grid-cols-7 gap-4 mt-5 text-center">
                                                    <div class="font-medium">Su</div>
                                                    <div class="font-medium">Mo</div>
                                                    <div class="font-medium">Tu</div>
                                                    <div class="font-medium">We</div>
                                                    <div class="font-medium">Th</div>
                                                    <div class="font-medium">Fr</div>
                                                    <div class="font-medium">Sa</div>
                                                    <div class="py-0.5 rounded relative text-gray-600">29</div>
                                                    <div class="py-0.5 rounded relative text-gray-600">30</div>
                                                    <div class="py-0.5 rounded relative text-gray-600">31</div>
                                                    <div class="py-0.5 rounded relative">1</div>
                                                    <div class="py-0.5 rounded relative">2</div>
                                                    <div class="py-0.5 rounded relative">3</div>
                                                    <div class="py-0.5 rounded relative">4</div>
                                                    <div class="py-0.5 rounded relative">5</div>
                                                    <div class="py-0.5 bg-theme-18 dark:bg-theme-9 rounded relative">6</div>
                                                    <div class="py-0.5 rounded relative">7</div>
                                                    <div class="py-0.5 bg-theme-1 dark:bg-theme-1 text-white rounded relative">8</div>
                                                    <div class="py-0.5 rounded relative">9</div>
                                                    <div class="py-0.5 rounded relative">10</div>
                                                    <div class="py-0.5 rounded relative">11</div>
                                                    <div class="py-0.5 rounded relative">12</div>
                                                    <div class="py-0.5 rounded relative">13</div>
                                                    <div class="py-0.5 rounded relative">14</div>
                                                    <div class="py-0.5 rounded relative">15</div>
                                                    <div class="py-0.5 rounded relative">16</div>
                                                    <div class="py-0.5 rounded relative">17</div>
                                                    <div class="py-0.5 rounded relative">18</div>
                                                    <div class="py-0.5 rounded relative">19</div>
                                                    <div class="py-0.5 rounded relative">20</div>
                                                    <div class="py-0.5 rounded relative">21</div>
                                                    <div class="py-0.5 rounded relative">22</div>
                                                    <div class="py-0.5 bg-theme-17 dark:bg-theme-11 rounded relative">23</div>
                                                    <div class="py-0.5 rounded relative">24</div>
                                                    <div class="py-0.5 rounded relative">25</div>
                                                    <div class="py-0.5 rounded relative">26</div>
                                                    <div class="py-0.5 bg-theme-14 dark:bg-theme-12 rounded relative">27</div>
                                                    <div class="py-0.5 rounded relative">28</div>
                                                    <div class="py-0.5 rounded relative">29</div>
                                                    <div class="py-0.5 rounded relative">30</div>
                                                    <div class="py-0.5 rounded relative text-gray-600">1</div>
                                                    <div class="py-0.5 rounded relative text-gray-600">2</div>
                                                    <div class="py-0.5 rounded relative text-gray-600">3</div>
                                                    <div class="py-0.5 rounded relative text-gray-600">4</div>
                                                    <div class="py-0.5 rounded relative text-gray-600">5</div>
                                                    <div class="py-0.5 rounded relative text-gray-600">6</div>
                                                    <div class="py-0.5 rounded relative text-gray-600">7</div>
                                                    <div class="py-0.5 rounded relative text-gray-600">8</div>
                                                    <div class="py-0.5 rounded relative text-gray-600">9</div>
                                                </div>
                                            </div>
                                            <div class="border-t border-gray-200 p-5">
                                                <div class="flex items-center">
                                                    <div class="w-2 h-2 bg-theme-11 rounded-full mr-3"></div>
                                                    <span class="truncate">UI/UX Workshop</span>
                                                    <div class="h-px flex-1 border border-r border-dashed border-gray-300 mx-3 xl:hidden"></div>
                                                    <span class="font-medium xl:ml-auto">23th</span>
                                                </div>
                                                <div class="flex items-center mt-4">
                                                    <div class="w-2 h-2 bg-theme-1 dark:bg-theme-10 rounded-full mr-3"></div>
                                                    <span class="truncate">VueJs Frontend Development</span>
                                                    <div class="h-px flex-1 border border-r border-dashed border-gray-300 mx-3 xl:hidden"></div>
                                                    <span class="font-medium xl:ml-auto">10th</span>
                                                </div>
                                                <div class="flex items-center mt-4">
                                                    <div class="w-2 h-2 bg-theme-12 rounded-full mr-3"></div>
                                                    <span class="truncate">Laravel Rest API</span>
                                                    <div class="h-px flex-1 border border-r border-dashed border-gray-300 mx-3 xl:hidden"></div>
                                                    <span class="font-medium xl:ml-auto">31th</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> --}}
                                <!-- END: Schedules -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END: Content -->
        </div>

        <!-- BEGIN: JS Assets-->
        <script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key=["your-google-map-api"]&libraries=places"></script>

        <!-- END: JS Assets-->
    </body>
</html>
