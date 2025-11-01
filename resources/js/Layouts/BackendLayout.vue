<template>
    <q-layout view="lHh LpR lff">
        <q-header height-hint="70" elevated style="background-color: #9177FE;" class="bg-white text-black">
            <q-toolbar style="min-height: 60px" class="q-px-lg">
                <q-btn  class="text-primary" round dense color="primary" icon="menu" @click="toggleLeftDrawer" />
                <q-toolbar-title>

                </q-toolbar-title>

                <div class="flex items-center q-gutter-sm">
                    <div v-show="$q.screen.gt.xs" style="line-height: 1">
                        <div class="text-lg text-weight-medium ">{{$page?.props?.auth?.user?.name}}</div>
                        <div class="text-sm text-weight-medium ">{{$page?.props?.auth?.user?.designation}}</div>
                    </div>
                    <q-btn-dropdown  dropdown-icon="account_circle">
                        <q-list dense>
                            <q-item clickable v-close-popup  @click.prevent="$inertia.get(route('profile.edit'))">
                                <q-item-section>Profile</q-item-section>
                            </q-item>
                            <q-item clickable v-close-popup   @click.prevent="$inertia.get(route('profile.edit-password'))">
                                <q-item-section>Change Password</q-item-section>
                            </q-item>
                            <q-separator />
                            <q-item clickable v-close-popup @click.prevent="$inertia.delete(route('login.destroy'))">
                                <q-item-section>Logout</q-item-section>
                            </q-item>
                        </q-list>
                    </q-btn-dropdown>

                    <q-icon size="21px" @click="$inertia.get(route('notification.list'))" class="cursor-pointer" name="notifications">
                        <q-badge v-if="hasNotification" floating color="negative" rounded/>
                    </q-icon>

                </div>
                <!-- Title with Dropdown Menu -->
            </q-toolbar>
        </q-header>

        <q-drawer width="250"  class="print-hide bg-transparent"  v-model="leftDrawerOpen" side="left">
            <q-list class="bg-transparent full-height">

                <div @click="$inertia.get(route('home'))"  class="column items-center q-gutter-md q-pa-lg bg-secondary text-white cursor-pointer">
                    <q-img src="/images/phed_logo.png" width="52px"/>
                    <div style="line-height: 1" class="text-lg text-grey-7 text-black-medium text-center">
                        Public Health Engineering Department
                        <span class="text-sm text-grey-7">(Government of Mizoram)</span>
                    </div>
                </div>
                <q-item :active="route().current()==='dashboard'" active
                        active-class="active-menu text-accent"
                        clickable
                        @click="$inertia.get(route('dashboard'))">
                    <q-item-section avatar>
                        <q-icon :active="route().current()==='dashboard'">
                            <svg v-if="route().current()==='dashboard'" width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path stroke="#306ADB" stroke-width="1.5" d="M3.75 3.75h8.5v5.5h-8.5zM15.75 3.75H21A3.25 3.25 0 0 1 24.25 7v8.25h-8.5V3.75zM15.75 18.75h8.5V21A3.25 3.25 0 0 1 21 24.25h-5.25v-5.5zM3.75 12.75h8.5v11.5h-8.5z"/>
                            </svg>
                            <svg v-else width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path stroke="#191C51" stroke-width="1.5" d="M3.75 3.75h8.5v5.5h-8.5zM15.75 3.75H21A3.25 3.25 0 0 1 24.25 7v8.25h-8.5V3.75z"/>
                                <path d="M15.75 18.75h8.5V21A3.25 3.25 0 0 1 21 24.25h-5.25v-5.5z" stroke="#D81D1D" stroke-width="1.5"/>
                                <path stroke="#191C51" stroke-width="1.5" d="M3.75 12.75h8.5v11.5h-8.5z"/>
                            </svg>
                        </q-icon>
                    </q-item-section>
                    <q-item-section>
                        <q-item-label>Dashboard</q-item-label>
                    </q-item-section>
                </q-item>

                <q-separator class="q-my-sm"/>


<!--             Admin   Employees-->
                <q-expansion-item
                    v-if="checkModules('employee')"
                    group="menu"
                    :label="module.employee.label"
                    :header-class="(
                              route().current()==='employees.all'
                             || route().current()==='employees.mr'
                             || route().current()==='employees.pe'
                             || route().current()==='employees.scheme'
                              || route().current()==='employees.deleted'
                             || route().current()==='employee.show'
                              || route().current()==='employee.create'
                              || route().current()==='employee.edit'

                          )

                          ?'active-menu text-accent':''"
                    icon="manage_accounts">

                    <template #header>
                        <q-item-section avatar>

                            <q-icon :active="
                              route().current()==='employees.all'
                             || route().current()==='employees.mr'
                             || route().current()==='employees.pe'
                             || route().current()==='employees.scheme'
                              || route().current()==='employees.deleted'
                             || route().current()==='employee.show'
                              || route().current()==='employee.create'
                              || route().current()==='employee.edit'

                        ">

                                <svg
                                    v-if="route().current()==='employees.all'
                             || route().current()==='employees.mr'
                             || route().current()==='employees.pe'
                             || route().current()==='employees.scheme'
                              || route().current()==='employees.deleted'
                             || route().current()==='employee.show'
                              || route().current()==='employee.create'
                              || route().current()==='employee.edit'"
                                    width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M6 4.5h13.5a.5.5 0 0 1 .5.5v6.938l1.5-1.52V5a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12a8 8 0 0 0 8 8h7.5a2 2 0 0 0 2-2v-3.038l-1.5 1.52V23a.5.5 0 0 1-.5.5H12A6.5 6.5 0 0 1 5.5 17V5a.5.5 0 0 1 .5-.5z" fill="#306ADB"/>
                                    <path fill="#306ADB" d="M5 18h7v1.5H5z"/>
                                    <path fill="#306ADB" d="M11 24v-6h1.5v6z"/>
                                    <circle cx="12.55" cy="9.25" r="1.65" stroke="#306ADB" stroke-width="1.2"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M15.457 15a3.496 3.496 0 0 0-2.873-1.5c-1.19 0-2.24.593-2.873 1.5H8a5.001 5.001 0 0 1 9.168 0h-1.711zM15.914 20.128v.698h.717l7.065-6.92-.707-.708-7.075 6.93zM23 11.088l-8.586 8.41v2.828h2.829l8.585-8.41L23 11.088z" fill="#306ADB"/>
                                </svg>
                                <svg v-else width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M6 4.5h13.5a.5.5 0 0 1 .5.5v6.938l1.5-1.52V5a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12a8 8 0 0 0 8 8h7.5a2 2 0 0 0 2-2v-3.038l-1.5 1.52V23a.5.5 0 0 1-.5.5H12A6.5 6.5 0 0 1 5.5 17V5a.5.5 0 0 1 .5-.5z" fill="#191C51"/>
                                    <path fill="#191C51" d="M5 18h7v1.5H5z"/>
                                    <path fill="#191C51" d="M11 24v-6h1.5v6z"/>
                                    <circle cx="12.551" cy="9.25" r="1.65" stroke="#191C51" stroke-width="1.2"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M15.457 15a3.496 3.496 0 0 0-2.873-1.5c-1.19 0-2.24.593-2.873 1.5H8a5.001 5.001 0 0 1 9.168 0h-1.711z" fill="#191C51"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M15.914 20.128v.698h.717l7.065-6.92-.707-.708-7.075 6.93zM23 11.088l-8.586 8.41v2.828h2.829l8.585-8.41L23 11.088z" fill="#D81D1D"/>
                                </svg>

                            </q-icon>
                        </q-item-section>

                        <q-item-section>
                            Employees
                        </q-item-section>
                    </template>

                    <template v-for="item in module.employee.children"
                              :key="item.route_name">
                        <q-item v-if="$page.props.permissions.find(val=>val.name===item.permission)"
                            class="nav-item"
                            :active="route().current()===item.route_name"
                            active-class="active-item text-accent"
                            clickable
                            @click="$inertia.get(route(item.route_name),{},{preserveState:true})">
                            <q-item-section avatar class="q-ml-sm">
                                <q-icon :size="route().current()===item.route_name ? '12px':'9px'"
                                        name="fiber_manual_record"/>
                            </q-item-section>
                            <q-item-section>
                                <q-item-label>{{ item.label }}</q-item-label>
                            </q-item-section>
                        </q-item>
                    </template>

                </q-expansion-item>

<!--                Manager Employees-->
                <q-expansion-item
                    v-if="checkModules('manager')"
                    group="menu"
                    :label="module.manager.label"
                    :header-class="(
                              route().current()==='employees.manager.all'
                             || route().current()==='employees.manager.mr'
                             || route().current()==='employees.manager.pe'
                             || route().current() ==='employees.manager.scheme'
                             || route().current()==='employee.show'
                              || route().current()==='employee.create'
                              || route().current()==='employee.edit'

                          )

                          ?'active-menu text-accent':''"
                    icon="manage_accounts">

                    <template #header>
                        <q-item-section avatar>

                            <q-icon :active="
                              route().current()==='employees.manager.all'
                             || route().current()==='employees.manager.mr'
                             || route().current()==='employees.manager.pe'
                             || route().current() ==='employees.manager.scheme'
                             || route().current()==='employee.show'
                              || route().current()==='employee.create'
                              || route().current()==='employee.edit'

                        ">

                                <svg
                                    v-if=" route().current()==='employees.manager.all'
                             || route().current()==='employees.manager.mr'
                             || route().current()==='employees.manager.pe'
                             || route().current() ==='employees.manager.scheme'
                             || route().current()==='employee.show'
                              || route().current()==='employee.create'
                              || route().current()==='employee.edit'"
                                    width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M6 4.5h13.5a.5.5 0 0 1 .5.5v6.938l1.5-1.52V5a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12a8 8 0 0 0 8 8h7.5a2 2 0 0 0 2-2v-3.038l-1.5 1.52V23a.5.5 0 0 1-.5.5H12A6.5 6.5 0 0 1 5.5 17V5a.5.5 0 0 1 .5-.5z" fill="#306ADB"/>
                                    <path fill="#306ADB" d="M5 18h7v1.5H5z"/>
                                    <path fill="#306ADB" d="M11 24v-6h1.5v6z"/>
                                    <circle cx="12.55" cy="9.25" r="1.65" stroke="#306ADB" stroke-width="1.2"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M15.457 15a3.496 3.496 0 0 0-2.873-1.5c-1.19 0-2.24.593-2.873 1.5H8a5.001 5.001 0 0 1 9.168 0h-1.711zM15.914 20.128v.698h.717l7.065-6.92-.707-.708-7.075 6.93zM23 11.088l-8.586 8.41v2.828h2.829l8.585-8.41L23 11.088z" fill="#306ADB"/>
                                </svg>
                                <svg v-else width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M6 4.5h13.5a.5.5 0 0 1 .5.5v6.938l1.5-1.52V5a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12a8 8 0 0 0 8 8h7.5a2 2 0 0 0 2-2v-3.038l-1.5 1.52V23a.5.5 0 0 1-.5.5H12A6.5 6.5 0 0 1 5.5 17V5a.5.5 0 0 1 .5-.5z" fill="#191C51"/>
                                    <path fill="#191C51" d="M5 18h7v1.5H5z"/>
                                    <path fill="#191C51" d="M11 24v-6h1.5v6z"/>
                                    <circle cx="12.551" cy="9.25" r="1.65" stroke="#191C51" stroke-width="1.2"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M15.457 15a3.496 3.496 0 0 0-2.873-1.5c-1.19 0-2.24.593-2.873 1.5H8a5.001 5.001 0 0 1 9.168 0h-1.711z" fill="#191C51"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M15.914 20.128v.698h.717l7.065-6.92-.707-.708-7.075 6.93zM23 11.088l-8.586 8.41v2.828h2.829l8.585-8.41L23 11.088z" fill="#D81D1D"/>
                                </svg>

                            </q-icon>
                        </q-item-section>

                        <q-item-section>
                            Employees
                        </q-item-section>
                    </template>

                    <template v-for="item in module.manager.children"
                              :key="item.route_name">
                        <q-item v-if="$page.props.permissions.find(val=>val.name===item.permission)"
                                class="nav-item"
                                :active="route().current()===item.route_name"
                                active-class="active-item text-accent"
                                clickable
                                @click="$inertia.get(route(item.route_name),{},{preserveState:true})">
                            <q-item-section avatar class="q-ml-sm">
                                <q-icon :size="route().current()===item.route_name ? '12px':'9px'"
                                        name="fiber_manual_record"/>
                            </q-item-section>
                            <q-item-section>
                                <q-item-label>{{ item.label }}</q-item-label>
                            </q-item-section>
                        </q-item>
                    </template>

                </q-expansion-item>

                <q-separator v-if="!isManager && !isViewer" class="q-my-sm"/>

                <!--                Remunerations-->
                <q-expansion-item v-if="checkModules('remunerations')"
                                  group="menu"
                                  :label="module.remunerations.label"
                                  :header-class="(
                               route().current()==='remuneration.detail'
                               || route().current()==='remuneration.summary'
                          )

                          ?'active-menu text-accent':''"
                                  icon="o_credit_card">
                    <template  #header>
                        <q-item-section avatar>

                            <q-icon>

                                <svg v-if=" route().current()==='remuneration.detail' ||
                                           route().current()==='remuneration.summary'" width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="7" y="18" width="13" height="1.5" fill="#306ADB"/>
                                    <rect x="7" y="14" width="5" height="1.5" fill="#306ADB"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M14 8H7C4.79086 8 3 9.79086 3 12V20C3 22.2091 4.79086 24 7 24H20C22.2091 24 24 22.2091 24 20V12C24 10.64 23.3213 9.43857 22.2841 8.71586L21.1959 9.80407C21.9729 10.2281 22.5 11.0525 22.5 12V20C22.5 21.3807 21.3807 22.5 20 22.5H7C5.61929 22.5 4.5 21.3807 4.5 20V12C4.5 10.6193 5.61929 9.5 7 9.5H14V8Z" fill="#306ADB"/>
                                    <path d="M22.6953 5.82031L22.3125 7.20312H15L15.3828 5.82031H22.6953ZM19.1562 14.375L15.2266 9.74219L15.2188 8.57812H17.0859C17.5703 8.57812 17.9688 8.49219 18.2812 8.32031C18.599 8.14323 18.8359 7.90625 18.9922 7.60938C19.1484 7.30729 19.2266 6.97135 19.2266 6.60156C19.2266 6.20052 19.1536 5.84635 19.0078 5.53906C18.862 5.23177 18.6302 4.99219 18.3125 4.82031C17.9948 4.64844 17.5755 4.5625 17.0547 4.5625H15.0078L15.4297 3H17.0547C17.9766 3 18.7422 3.13802 19.3516 3.41406C19.9609 3.6901 20.4167 4.09115 20.7188 4.61719C21.026 5.14323 21.1797 5.77865 21.1797 6.52344C21.1797 7.17448 21.0677 7.75 20.8438 8.25C20.6198 8.74479 20.2578 9.14583 19.7578 9.45312C19.2578 9.75521 18.5938 9.94271 17.7656 10.0156L21.3594 14.2734V14.375H19.1562ZM22.7031 3L22.3125 4.38281H16.3359L16.7188 3H22.7031Z" fill="#306ADB"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M14.8148 11.3887C14.3206 11.7528 14 12.3389 14 12.9998V13.9998C14 15.1044 14.8954 15.9998 16 15.9998H18C18.309 15.9998 18.6016 15.9298 18.8628 15.8047L17.6667 14.4998H16C15.7239 14.4998 15.5 14.276 15.5 13.9998V12.9998C15.5 12.7747 15.6488 12.5843 15.8534 12.5217L14.8148 11.3887Z" fill="#306ADB"/>
                                </svg>
                                <svg v-else width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="7" y="18" width="13" height="1.5" fill="#191C51"/>
                                    <rect x="7" y="14" width="5" height="1.5" fill="#191C51"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M14 8H7C4.79086 8 3 9.79086 3 12V20C3 22.2091 4.79086 24 7 24H20C22.2091 24 24 22.2091 24 20V12C24 10.64 23.3213 9.43857 22.2841 8.71586L21.1959 9.80407C21.9729 10.2281 22.5 11.0525 22.5 12V20C22.5 21.3807 21.3807 22.5 20 22.5H7C5.61929 22.5 4.5 21.3807 4.5 20V12C4.5 10.6193 5.61929 9.5 7 9.5H14V8Z" fill="#191C51"/>
                                    <path d="M22.6953 5.82031L22.3125 7.20312H15L15.3828 5.82031H22.6953ZM19.1562 14.375L15.2266 9.74219L15.2188 8.57812H17.0859C17.5703 8.57812 17.9688 8.49219 18.2812 8.32031C18.599 8.14323 18.8359 7.90625 18.9922 7.60938C19.1484 7.30729 19.2266 6.97135 19.2266 6.60156C19.2266 6.20052 19.1536 5.84635 19.0078 5.53906C18.862 5.23177 18.6302 4.99219 18.3125 4.82031C17.9948 4.64844 17.5755 4.5625 17.0547 4.5625H15.0078L15.4297 3H17.0547C17.9766 3 18.7422 3.13802 19.3516 3.41406C19.9609 3.6901 20.4167 4.09115 20.7188 4.61719C21.026 5.14323 21.1797 5.77865 21.1797 6.52344C21.1797 7.17448 21.0677 7.75 20.8438 8.25C20.6198 8.74479 20.2578 9.14583 19.7578 9.45312C19.2578 9.75521 18.5938 9.94271 17.7656 10.0156L21.3594 14.2734V14.375H19.1562ZM22.7031 3L22.3125 4.38281H16.3359L16.7188 3H22.7031Z" fill="#D81D1D"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M14.8148 11.3887C14.3206 11.7528 14 12.3389 14 12.9998V13.9998C14 15.1044 14.8954 15.9998 16 15.9998H18C18.309 15.9998 18.6016 15.9298 18.8628 15.8047L17.6667 14.4998H16C15.7239 14.4998 15.5 14.276 15.5 13.9998V12.9998C15.5 12.7747 15.6488 12.5843 15.8534 12.5217L14.8148 11.3887Z" fill="#191C51"/>
                                </svg>


                            </q-icon>
                        </q-item-section>
                        <q-item-section>
                            Remunerations
                        </q-item-section>
                    </template>
                    <template v-for="item in module.remunerations.children"
                              :key="item.route_name">

                        <q-item v-if="$page.props.permissions.find(val=>val.name===item.permission)"
                                :active="route().current()===item.route_name"
                                active-class="active-item text-accent"
                                class="nav-item"
                                clickable
                                @click="$inertia.get(route(item.route_name))">
                            <q-item-section avatar class="q-ml-sm">
                                <q-icon :size="route().current()===item.route_name ? '12px':'9px'"
                                        name="fiber_manual_record"/>
                            </q-item-section>
                            <q-item-section>
                                <q-item-label>{{ item.label }}</q-item-label>
                            </q-item-section>
                        </q-item>
                    </template>
                </q-expansion-item>
                <q-separator  class="q-my-sm"/>

<!--                Manager MIS-->
                <q-expansion-item v-if="checkModules('manager_mis')"
                                  group="menu"
                                  :label="module.manager_mis.label"
                                  :header-class="(
                              route().current()==='mis.manager.remuneration'
                             || route().current()==='mis.manager.engagement_card'

                          )

                          ?'active-menu text-accent':''"
                                  icon="o_credit_card">
                    <template  #header>
                        <q-item-section avatar>

                            <q-icon>
                                <svg v-if="route().current()==='mis.manager.remuneration'
                             || route().current()==='mis.manager.engagement_card'" width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="14" y="7" width="1.5" height="7" fill="#306ADB"/>
                                    <rect x="18" y="8" width="1.5" height="6" fill="#306ADB"/>
                                    <rect x="22" y="4" width="1.5" height="10" fill="#306ADB"/>
                                    <rect x="12" y="14" width="13" height="1.5" fill="#306ADB"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 7.93734L4.52 7C3.68053 7 3 7.89543 3 9V21C3 22.1046 3.68053 23 4.52 23L12.5 24L20.48 23C21.3195 23 22 22.1046 22 21V17H20.38V21C20.38 21.2761 20.2099 21.5 20 21.5L12.5 22.5L4.88 21.5C4.67013 21.5 4.5 21.2761 4.5 21V9C4.5 8.72386 4.67013 8.5 4.88 8.5L12 9.43438V7.93734Z" fill="#306ADB"/>
                                    <rect x="12" y="17" width="1.5" height="6" fill="#306ADB"/>
                                    <path d="M6.00037 16.4034L6.00038 14.9158L10.6906 15.4994L10.6905 17L6.00037 16.4034Z" fill="#306ADB"/>
                                    <path d="M6.00037 12.5118L6.00038 11.0242L10.6906 11.6078L10.6905 13.1084L6.00037 12.5118Z" fill="#306ADB"/>
                                </svg>
                                <svg v-else width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="14" y="7" width="1.5" height="7" fill="#D81D1D"/>
                                    <rect x="18" y="8" width="1.5" height="6" fill="#D81D1D"/>
                                    <rect x="22" y="4" width="1.5" height="10" fill="#D81D1D"/>
                                    <rect x="12" y="14" width="13" height="1.5" fill="#191C51"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 7.93734L4.52 7C3.68053 7 3 7.89543 3 9V21C3 22.1046 3.68053 23 4.52 23L12.5 24L20.48 23C21.3195 23 22 22.1046 22 21V17H20.38V21C20.38 21.2761 20.2099 21.5 20 21.5L12.5 22.5L4.88 21.5C4.67013 21.5 4.5 21.2761 4.5 21V9C4.5 8.72386 4.67013 8.5 4.88 8.5L12 9.43438V7.93734Z" fill="#191C51"/>
                                    <rect x="12" y="17" width="1.5" height="6" fill="#191C51"/>
                                    <path d="M5.99988 16.4034L5.99989 14.9158L10.6901 15.4994L10.6901 17L5.99988 16.4034Z" fill="#191C51"/>
                                    <path d="M5.99988 12.5118L5.99989 11.0242L10.6901 11.6078L10.6901 13.1084L5.99988 12.5118Z" fill="#191C51"/>
                                </svg>
                            </q-icon>
                        </q-item-section>
                        <q-item-section>
                            MIS
                        </q-item-section>
                    </template>
                    <template v-for="item in module.manager_mis.children"
                              :key="item.route_name">

                        <q-item v-if="$page.props.permissions.find(val=>val.name===item.permission)"
                                :active="route().current()===item.route_name"
                                active-class="active-item text-accent"
                                class="nav-item"
                                clickable
                                @click="$inertia.get(route(item.route_name))">
                            <q-item-section avatar class="q-ml-sm">
                                <q-icon :size="route().current()===item.route_name ? '12px':'9px'"
                                        name="fiber_manual_record"/>
                            </q-item-section>
                            <q-item-section>
                                <q-item-label>{{ item.label }}</q-item-label>
                            </q-item-section>
                        </q-item>
                    </template>
                </q-expansion-item>

<!--               Admin MIS-->
                <q-expansion-item v-if="checkModules('mis')"
                    group="menu"
                    :label="module.mis.label"
                    :header-class="(
                              route().current()==='mis.export'
                             || route().current()==='mis.import'
                              || route().current()==='mis.remuneration'
                               || route().current()==='mis.engagement-card'
                          )

                          ?'active-menu text-accent':''"
                    icon="o_credit_card">
                    <template  #header>
                        <q-item-section avatar>

                            <q-icon>
                                <svg v-if=" route().current()==='mis.export' ||
                                           route().current()==='mis.import'  || route().current()==='mis.remuneration'
                               || route().current()==='mis.engagement-card'" width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="14" y="7" width="1.5" height="7" fill="#306ADB"/>
                                    <rect x="18" y="8" width="1.5" height="6" fill="#306ADB"/>
                                    <rect x="22" y="4" width="1.5" height="10" fill="#306ADB"/>
                                    <rect x="12" y="14" width="13" height="1.5" fill="#306ADB"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 7.93734L4.52 7C3.68053 7 3 7.89543 3 9V21C3 22.1046 3.68053 23 4.52 23L12.5 24L20.48 23C21.3195 23 22 22.1046 22 21V17H20.38V21C20.38 21.2761 20.2099 21.5 20 21.5L12.5 22.5L4.88 21.5C4.67013 21.5 4.5 21.2761 4.5 21V9C4.5 8.72386 4.67013 8.5 4.88 8.5L12 9.43438V7.93734Z" fill="#306ADB"/>
                                    <rect x="12" y="17" width="1.5" height="6" fill="#306ADB"/>
                                    <path d="M6.00037 16.4034L6.00038 14.9158L10.6906 15.4994L10.6905 17L6.00037 16.4034Z" fill="#306ADB"/>
                                    <path d="M6.00037 12.5118L6.00038 11.0242L10.6906 11.6078L10.6905 13.1084L6.00037 12.5118Z" fill="#306ADB"/>
                                </svg>
                                <svg v-else width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="14" y="7" width="1.5" height="7" fill="#D81D1D"/>
                                    <rect x="18" y="8" width="1.5" height="6" fill="#D81D1D"/>
                                    <rect x="22" y="4" width="1.5" height="10" fill="#D81D1D"/>
                                    <rect x="12" y="14" width="13" height="1.5" fill="#191C51"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 7.93734L4.52 7C3.68053 7 3 7.89543 3 9V21C3 22.1046 3.68053 23 4.52 23L12.5 24L20.48 23C21.3195 23 22 22.1046 22 21V17H20.38V21C20.38 21.2761 20.2099 21.5 20 21.5L12.5 22.5L4.88 21.5C4.67013 21.5 4.5 21.2761 4.5 21V9C4.5 8.72386 4.67013 8.5 4.88 8.5L12 9.43438V7.93734Z" fill="#191C51"/>
                                    <rect x="12" y="17" width="1.5" height="6" fill="#191C51"/>
                                    <path d="M5.99988 16.4034L5.99989 14.9158L10.6901 15.4994L10.6901 17L5.99988 16.4034Z" fill="#191C51"/>
                                    <path d="M5.99988 12.5118L5.99989 11.0242L10.6901 11.6078L10.6901 13.1084L5.99988 12.5118Z" fill="#191C51"/>
                                </svg>
                            </q-icon>
                        </q-item-section>
                        <q-item-section>
                            MIS
                        </q-item-section>
                    </template>
                    <template v-for="item in module.mis.children"
                              :key="item.route_name">

                        <q-item v-if="$page.props.permissions.find(val=>val.name===item.permission)"
                                :active="route().current()===item.route_name"
                                active-class="active-item text-accent"
                                class="nav-item"
                                clickable
                                @click="$inertia.get(route(item.route_name))">
                            <q-item-section avatar class="q-ml-sm">
                                <q-icon :size="route().current()===item.route_name ? '12px':'9px'"
                                                        name="fiber_manual_record"/>
                            </q-item-section>
                            <q-item-section>
                                <q-item-label>{{ item.label }}</q-item-label>
                            </q-item-section>
                        </q-item>
                    </template>
                </q-expansion-item>

                <q-separator class="q-my-sm"/>
                <!--                Administration-->
                <q-expansion-item v-if="checkModules('administration')"
                                  group="menu"
                                  :label="module.admin.label"
                                  :header-class="(
                              route().current()==='user.index'
                             || route().current()==='user.create'
                             || route().current()==='user.edit'

                              || route().current()==='office.index'
                               || route().current()==='office.create'
                                 || route().current()==='office.edit'

                               || route().current()==='document-type.index'
                              || route().current()==='document-type.create'
                             || route().current()==='document-type.store'

                             || route().current()==='role.index'
                             || route().current()==='role.edit'

                          )

                          ?'active-menu text-accent':''"
                                  icon="o_manage_accounts">

                    <template #header>
                        <q-item-section avatar>

                            <q-icon :active=" route().current()==='user.index'
                             || route().current()==='user.create'
                             || route().current()==='user.edit'

                              || route().current()==='office.index'
                               || route().current()==='office.create'
                                 || route().current()==='office.edit'

                               || route().current()==='document-type.index'
                              || route().current()==='document-type.create'
                             || route().current()==='document-type.store'

                             || route().current()==='role.index'
                             || route().current()==='role.edit'
                        ">

                                <svg v-if="route().current()==='user.index'
                             || route().current()==='user.create'
                             || route().current()==='user.edit'

                              || route().current()==='office.index'
                               || route().current()==='office.create'
                                 || route().current()==='office.edit'

                               || route().current()==='document-type.index'
                              || route().current()==='document-type.create'
                             || route().current()==='document-type.store'

                             || route().current()==='role.index'
                             || route().current()==='role.edit'" width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M21 17.5H11C10.7239 17.5 10.5 17.7239 10.5 18V23C10.5 23.2761 10.7239 23.5 11 23.5H21C21.2761 23.5 21.5 23.2761 21.5 23V18C21.5 17.7239 21.2761 17.5 21 17.5ZM11 16C9.89543 16 9 16.8954 9 18V23C9 24.1046 9.89543 25 11 25H21C22.1046 25 23 24.1046 23 23V18C23 16.8954 22.1046 16 21 16H11Z" fill="#306ADB"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M13.8333 4H6C4.89543 4 4 4.89543 4 6V19C4 20.1046 4.89543 21 6 21H9.5V19.5H6C5.72386 19.5 5.5 19.2761 5.5 19V6C5.5 5.72386 5.72386 5.5 6 5.5H12.8333L13.8333 4ZM14.5 14.6835V17H16V15.4038C15.756 15.3333 15.4226 15.2113 15 15C14.8165 14.9082 14.6498 14.7997 14.5 14.6835Z" fill="#306ADB"/>
                                    <rect x="8" y="9.5" width="1.5" height="4" transform="rotate(-90 8 9.5)" fill="#306ADB"/>
                                    <path d="M8 12.5L8 11L12 11L12 12.5L8 12.5Z" fill="#306ADB"/>
                                    <path d="M12 20.5L12 19L16 19L16 20.5L12 20.5Z" fill="#306ADB"/>
                                    <path d="M24.8475 8.87916C24.938 8.8636 25.0044 8.78346 24.9998 8.69192C24.9881 8.45644 24.9614 8.21915 24.9204 7.98133C24.8792 7.74222 24.8242 7.5083 24.7565 7.28138C24.7303 7.19372 24.6418 7.14004 24.5516 7.15508C24.2082 7.21214 23.8277 7.27645 23.6236 7.3138C23.5364 7.32962 23.4524 7.2806 23.4197 7.19839C23.1855 6.6097 22.8489 6.11592 22.4165 5.67375L22.4186 5.67219L23.0488 4.77981C23.102 4.7046 23.0919 4.60035 23.0234 4.53888C22.6689 4.22016 22.278 3.94422 21.8594 3.71756C21.7785 3.67373 21.677 3.69915 21.6239 3.77436L20.9944 4.66544L20.9926 4.66803C20.3984 4.39417 19.7424 4.24064 19.0629 4.23157L19.0637 4.22897L18.8785 3.15246C18.8629 3.06195 18.7828 2.99556 18.6912 3.00023C18.4557 3.0119 18.2184 3.03861 17.9806 3.07959C17.7427 3.12056 17.5109 3.17451 17.2852 3.24219C17.1973 3.26865 17.1441 3.35786 17.1597 3.44837L17.3194 4.37706C17.3342 4.4629 17.2857 4.54614 17.2048 4.57882C16.6199 4.81456 16.1074 5.15948 15.6707 5.58635L15.6691 5.58427L14.7774 4.9546C14.7024 4.9017 14.5987 4.91155 14.5372 4.9795C14.218 5.33272 13.9417 5.7238 13.715 6.14341C13.6715 6.22432 13.6966 6.3252 13.7718 6.37837L14.5437 6.92349C14.6148 6.97381 14.6394 7.06665 14.6054 7.14678C14.364 7.71681 14.2312 8.33766 14.2283 8.98082C14.2294 8.96656 14.2281 8.95152 14.2278 8.93621L13.1525 9.12112C13.062 9.13668 12.9956 9.21682 13.0002 9.30836C13.0119 9.54384 13.0386 9.78114 13.0796 10.0189C13.1206 10.257 13.1748 10.4894 13.2425 10.7153C13.2689 10.8032 13.3582 10.8564 13.4487 10.8408L14.3764 10.6813C14.4623 10.6665 14.5455 10.715 14.578 10.7957C14.7935 11.3296 15.0998 11.8105 15.4769 12.222C15.5358 12.2863 15.5436 12.382 15.4935 12.4534L14.9514 13.2205C14.8983 13.2957 14.9084 13.3999 14.9769 13.4614C15.3311 13.7801 15.7223 14.0561 16.1409 14.2827C16.2218 14.3266 16.3232 14.3011 16.3764 14.2259L16.9174 13.4598C16.9677 13.3885 17.0606 13.3641 17.141 13.3981C17.6551 13.616 18.2111 13.7449 18.7877 13.7703C18.8749 13.7742 18.948 13.8361 18.9628 13.922L19.122 14.8476C19.1376 14.9381 19.2177 15.0042 19.3093 14.9998C19.5445 14.9881 19.7808 14.9617 20.0189 14.9207C20.257 14.8797 20.4894 14.8255 20.7153 14.7578C20.8032 14.7314 20.8564 14.6422 20.8408 14.5519L20.6816 13.6266C20.6668 13.5408 20.7153 13.4578 20.796 13.4251C21.329 13.2096 21.8096 12.9025 22.2212 12.5254C22.2855 12.4666 22.3815 12.4585 22.4525 12.5091L23.219 13.0511C23.2942 13.1043 23.3982 13.0944 23.4599 13.026C23.7789 12.6725 24.0557 12.2814 24.2834 11.8626C24.3275 11.7817 24.3021 11.68 24.2266 11.6268L23.4584 11.0851C23.387 11.0348 23.3624 10.9419 23.3964 10.8618C23.6142 10.3468 23.7431 9.79073 23.7686 9.21422C23.7725 9.12709 23.8344 9.05395 23.9203 9.03917L24.848 8.87942L24.8475 8.87916ZM19.564 12.2814C17.7508 12.5934 16.0296 11.3779 15.7176 9.56485C15.4056 7.75182 16.621 6.02956 18.4342 5.71757C20.2474 5.40559 21.9701 6.62214 22.2821 8.43517C22.5942 10.2482 21.3772 11.9694 19.564 12.2814Z" fill="#306ADB"/>
                                </svg>
                                <svg v-else width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M21 17.5H11C10.7239 17.5 10.5 17.7239 10.5 18V23C10.5 23.2761 10.7239 23.5 11 23.5H21C21.2761 23.5 21.5 23.2761 21.5 23V18C21.5 17.7239 21.2761 17.5 21 17.5ZM11 16C9.89543 16 9 16.8954 9 18V23C9 24.1046 9.89543 25 11 25H21C22.1046 25 23 24.1046 23 23V18C23 16.8954 22.1046 16 21 16H11Z" fill="#191C51"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M13.8333 4H6C4.89543 4 4 4.89543 4 6V19C4 20.1046 4.89543 21 6 21H9.5V19.5H6C5.72386 19.5 5.5 19.2761 5.5 19V6C5.5 5.72386 5.72386 5.5 6 5.5H12.8333L13.8333 4ZM14.5 14.6835V17H16V15.4038C15.756 15.3333 15.4226 15.2113 15 15C14.8165 14.9082 14.6498 14.7997 14.5 14.6835Z" fill="#191C51"/>
                                    <rect x="8" y="9.5" width="1.5" height="4" transform="rotate(-90 8 9.5)" fill="#191C51"/>
                                    <path d="M8 12.5L8 11L12 11L12 12.5L8 12.5Z" fill="#191C51"/>
                                    <path d="M12 20.5L12 19L16 19L16 20.5L12 20.5Z" fill="#191C51"/>
                                    <path d="M24.8475 8.87916C24.938 8.8636 25.0044 8.78346 24.9998 8.69192C24.9881 8.45644 24.9614 8.21915 24.9204 7.98133C24.8792 7.74222 24.8242 7.5083 24.7565 7.28138C24.7303 7.19372 24.6418 7.14004 24.5516 7.15508C24.2082 7.21214 23.8277 7.27645 23.6236 7.3138C23.5364 7.32962 23.4524 7.2806 23.4197 7.19839C23.1855 6.6097 22.8489 6.11592 22.4165 5.67375L22.4186 5.67219L23.0488 4.77981C23.102 4.7046 23.0919 4.60035 23.0234 4.53888C22.6689 4.22016 22.278 3.94422 21.8594 3.71756C21.7785 3.67373 21.677 3.69915 21.6239 3.77436L20.9944 4.66544L20.9926 4.66803C20.3984 4.39417 19.7424 4.24064 19.0629 4.23157L19.0637 4.22897L18.8785 3.15246C18.8629 3.06195 18.7828 2.99556 18.6912 3.00023C18.4557 3.0119 18.2184 3.03861 17.9806 3.07959C17.7427 3.12056 17.5109 3.17451 17.2852 3.24219C17.1973 3.26865 17.1441 3.35786 17.1597 3.44837L17.3194 4.37706C17.3342 4.4629 17.2857 4.54614 17.2048 4.57882C16.6199 4.81456 16.1074 5.15948 15.6707 5.58635L15.6691 5.58427L14.7774 4.9546C14.7024 4.9017 14.5987 4.91155 14.5372 4.9795C14.218 5.33272 13.9417 5.7238 13.715 6.14341C13.6715 6.22432 13.6966 6.3252 13.7718 6.37837L14.5437 6.92349C14.6148 6.97381 14.6394 7.06665 14.6054 7.14678C14.364 7.71681 14.2312 8.33766 14.2283 8.98082C14.2294 8.96656 14.2281 8.95152 14.2278 8.93621L13.1525 9.12112C13.062 9.13668 12.9956 9.21682 13.0002 9.30836C13.0119 9.54384 13.0386 9.78114 13.0796 10.0189C13.1206 10.257 13.1748 10.4894 13.2425 10.7153C13.2689 10.8032 13.3582 10.8564 13.4487 10.8408L14.3764 10.6813C14.4623 10.6665 14.5455 10.715 14.578 10.7957C14.7935 11.3296 15.0998 11.8105 15.4769 12.222C15.5358 12.2863 15.5436 12.382 15.4935 12.4534L14.9514 13.2205C14.8983 13.2957 14.9084 13.3999 14.9769 13.4614C15.3311 13.7801 15.7223 14.0561 16.1409 14.2827C16.2218 14.3266 16.3232 14.3011 16.3764 14.2259L16.9174 13.4598C16.9677 13.3885 17.0606 13.3641 17.141 13.3981C17.6551 13.616 18.2111 13.7449 18.7877 13.7703C18.8749 13.7742 18.948 13.8361 18.9628 13.922L19.122 14.8476C19.1376 14.9381 19.2177 15.0042 19.3093 14.9998C19.5445 14.9881 19.7808 14.9617 20.0189 14.9207C20.257 14.8797 20.4894 14.8255 20.7153 14.7578C20.8032 14.7314 20.8564 14.6422 20.8408 14.5519L20.6816 13.6266C20.6668 13.5408 20.7153 13.4578 20.796 13.4251C21.329 13.2096 21.8096 12.9025 22.2212 12.5254C22.2855 12.4666 22.3815 12.4585 22.4525 12.5091L23.219 13.0511C23.2942 13.1043 23.3982 13.0944 23.4599 13.026C23.7789 12.6725 24.0557 12.2814 24.2834 11.8626C24.3275 11.7817 24.3021 11.68 24.2266 11.6268L23.4584 11.0851C23.387 11.0348 23.3624 10.9419 23.3964 10.8618C23.6142 10.3468 23.7431 9.79073 23.7686 9.21422C23.7725 9.12709 23.8344 9.05395 23.9203 9.03917L24.848 8.87942L24.8475 8.87916ZM19.564 12.2814C17.7508 12.5934 16.0296 11.3779 15.7176 9.56485C15.4056 7.75182 16.621 6.02956 18.4342 5.71757C20.2474 5.40559 21.9701 6.62214 22.2821 8.43517C22.5942 10.2482 21.3772 11.9694 19.564 12.2814Z" fill="#D81D1D"/>
                                </svg>

                            </q-icon>
                        </q-item-section>

                        <q-item-section>
                            Administration
                        </q-item-section>
                    </template>

                    <template v-for="item in module.admin.children"
                              :key="item.route_name">
                        <q-item
                            v-if="$page.props.permissions.find(val=>val.name===item.permission)"
                                class="nav-item"
                                :active="route().current()===item.route_name"
                                active-class="active-item text-accent"
                                clickable
                                @click="$inertia.get(route(item.route_name),{},{preserveState:true})">
                            <q-item-section avatar class="q-ml-sm">
                                <q-icon :size="route().current()===item.route_name ? '12px':'9px'"
                                        name="fiber_manual_record"/>
                            </q-item-section>
                            <q-item-section>
                                <q-item-label>{{ item.label }}</q-item-label>
                            </q-item-section>
                        </q-item>
                    </template>

                </q-expansion-item>

            </q-list>
        </q-drawer>

        <q-page-container>
            <slot/>
        </q-page-container>

        <q-footer  class="bg-white text-black q-pa-md">
            <q-toolbar class="container flex justify-between items-center">
                <div class="flex q-gutter-sm items-center">
                    <q-img src="/images/msegs.png" width="46px"/>
                    <div>
                        <div>Crafted with care by</div>
                        <div>Mizoram State e-Governance Society</div>
                    </div>
                </div>

            </q-toolbar>
        </q-footer>

    </q-layout>
</template>

<script setup>
import {reactive,computed,ref, onMounted} from 'vue'

import {usePage, router} from "@inertiajs/vue3";
import {useQuasar} from "quasar";


const leftDrawerOpen = ref(false)


const toggleLeftDrawer = () => {
    leftDrawerOpen.value = !leftDrawerOpen.value
}


const module = reactive({

    manager: {
        label: 'Employees',
        children: [
            {route_name: 'employees.manager.all', label: 'All Employee', permission: 'view-allemployee'},
            {route_name: 'employees.manager.wc', label: 'Work Charge Employee', permission: 'view-wc-employee'},
            {route_name: 'employees.manager.pe', label: 'Provisional Employee', permission: 'view-pe-employee'},
            {route_name: 'employees.manager.mr', label: 'Muster Roll Employee', permission: 'view-mr-employee'},
            {route_name: 'employees.manager.scheme', label: 'Scheme Employee', permission: 'view-scheme-employee'},
            {route_name: 'employees.manager.deleted', label: 'Deleted Employee', permission: 'view-deleted-employee'},
        ]
    },

    manager_mis:{
        label: 'MIS',
        children: [
            {route_name: 'mis.manager-remuneration', label: 'Remuneration', permission: 'view-remuneration'},
            {route_name: 'mis.manager-engagement-card', label: 'Engagement Card', permission: 'download-engagement-card'},
        ]
    },

    employee: {
        label: 'Employees',
        children: [
            {route_name: 'employees.all', label: 'All Employee', permission: 'view-allemployee'},
            {route_name: 'employees.wc', label: 'Work charge Employee', permission: 'view-wc-employee'},
            {route_name: 'employees.pe', label: 'Provisional Employee', permission: 'view-pe-employee'},
            {route_name: 'employees.mr', label: 'Muster Roll Employee', permission: 'view-mr-employee'},
            {route_name: 'employees.scheme', label: 'Scheme Employee', permission: 'view-scheme-employee'},
            {route_name: 'employees.deleted', label: 'Deleted Employee', permission: 'view-deleted-employee'},
            {route_name: 'employees.trashed', label: 'Trashed Employees', permission: 'view-trashed-employee'},

        ]
    },


    remunerations: {
        label: 'Remuneration',
        children: [
            {route_name: 'remuneration.summary', label: 'Summary', permission: 'view-remuneration'},
            {route_name: 'remuneration.detail', label: 'Details', permission: 'generate-remuneration'},

        ]
    },

    mis: {
        label: 'MIS',
        children: [
            {route_name: 'mis.export', label: 'Export', permission: 'export-employee'},
            {route_name: 'mis.import', label: 'Import', permission: 'import-employee'},
            {route_name: 'mis.import-work-charge', label: 'Import Work Charge Employee', permission: 'import-employee'},
            {route_name: 'mis.import-engagement', label: 'Import Date of Engagement', permission: 'import-employee'},
            {route_name: 'mis.create-wc-employee', label: 'Add WC Employee', permission: 'create-employee'},
            {route_name: 'mis.create-pe-employee', label: 'Add PE Employee', permission: 'create-employee'},
            {route_name: 'mis.create-mr-employee', label: 'Add MR Employee', permission: 'create-employee'},
            {route_name: 'mis.engagement-card', label: 'Engagement Card', permission: 'generate-engagement-card'},
            {route_name: 'mis.employee-document', label: 'Employees Document', permission: 'update-document'},
            {route_name: 'employees.master', label: 'Master Employees', permission: 'view-master-employee'},
        ]
    },


    admin: {
        label: 'Administration',
        children: [
            {route_name: 'office.index', label: 'Offices', permission: 'view-office'},
            {route_name: 'document-type.index', label: 'Document Type', permission: 'view-any-document-type'},
            {route_name: 'scheme.index', label: 'Schemes', permission: 'view-any-document-type'},

            {route_name: 'role.index', label: 'Permissions', permission: 'view-anyrole'},
            {route_name: 'user.index', label: 'User Accounts', permission: 'view-anyuser'},


        ]
    },





})


const MENUS = {
    Manager: ['manager', 'manager_mis' ],
    Reviewer: ['employee','remunerations', 'mis',],
    Admin: ['employee','remunerations', 'mis', 'administration'],
    Viewer: ['manager', 'manager_mis' ],
};


const checkModules = (module) => {
    const role = usePage().props.roles[0] || 'Admin';
    const availableModules = MENUS[role] || [];

    return availableModules.includes(module);
};

const isAdmin = computed(() => !!usePage().props.roles?.find(item => item === 'Admin'));
const isManager = computed(() => !!usePage().props.roles?.find(item => item === 'Manager'));

const isReviewer = computed(() => !!usePage().props.roles?.find(item => item === 'Reviewer'));

const isViewer = computed(() => !!usePage().props.roles?.find(item => item === 'Viewer'));

const notificationCount = ref(0);

const hasNotification = computed(() => { return (isAdmin.value || isManager.value) && notificationCount.value > 0; });

async function fetchNotifications() {
    try {
        const { data } = await axios.get(route('notifications.count'));
        notificationCount.value = data.count;
    } catch (e) {
        console.error('Failed to fetch notification count', e);
    }
}

onMounted(fetchNotifications);

// Update notifications on every navigation
router.on('navigate', () => {
    fetchNotifications();
});

</script>




