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
                            <q-item clickable v-close-popup>
                                <q-item-section>Profile</q-item-section>
                            </q-item>
                            <q-item clickable v-close-popup>
                                <q-item-section>Change Password</q-item-section>
                            </q-item>
                            <q-separator />
                            <q-item clickable v-close-popup @click.prevent="$inertia.delete(route('login.destroy'))">
                                <q-item-section>Logout</q-item-section>
                            </q-item>
                        </q-list>
                    </q-btn-dropdown>

                </div>
                <!-- Title with Dropdown Menu -->
            </q-toolbar>
        </q-header>

        <q-drawer width="250"  class="print-hide bg-transparent"  v-model="leftDrawerOpen" side="left">
            <q-list class="bg-transparent full-height">

                <div class="column items-center q-gutter-md q-pa-lg bg-secondary text-white">
                    <q-img src="/images/phed_logo.png" width="46px"/>
                    <div style="line-height: 1" class="text-lg text-grey-7 text-black-medium text-center">
                        PHED Department
                        <span class="text-sm text-grey-7">(Government of Mizoram)</span>
                    </div>
                </div>
                <q-item :active="route().current()==='admin.dashboard'" active
                        active-class="active-menu text-accent"
                        clickable
                        @click="$inertia.get(route('admin.dashboard'))">
                    <q-item-section avatar>
                        <q-icon :active="route().current()==='admin.dashboard'">
                            <svg v-if="active" width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
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


<!--                <q-expansion-item v-if="checkModules('registration')"-->
<!--                                  group="menu"-->
<!--                                  :label="module.registration.label"-->
<!--                                  :header-class="(-->
<!--                               route().current()==='apply.step-one'-->
<!--                             || route().current()==='apply.step-two'-->
<!--                             || route().current()==='apply.step-last'-->
<!--                             || route().current()==='apply.verify-payment'-->
<!--                             || route().current()==='new-application.index'-->
<!--                             || route().current()==='my-application.index'-->
<!--                             || route().current()==='application.show'-->
<!--                             || route().current()==='certificate.due'-->
<!--                             || route().current()==='certificate.completed'-->
<!--                             || route().current()==='pendency.index'-->
<!--                             || route().current()==='archived-application.index'-->
<!--                             || route().current()==='user-application.certificate'-->
<!--                             || route().current()==='dissolution.create'-->
<!--                          )?'active-menu text-accent':''"-->
<!--                                  icon="how_to_reg">-->
<!--                    <template #header>-->
<!--                        <q-item-section avatar>-->
<!--                            <ZRegistration :active="(-->
<!--                         route().current()==='apply.step-one'-->
<!--                             || route().current()==='apply.step-two'-->
<!--                             || route().current()==='apply.step-last'-->
<!--                             || route().current()==='apply.verify-payment'-->
<!--                             || route().current()==='new-application.index'-->
<!--                             || route().current()==='my-application.index'-->
<!--                             || route().current()==='application.show'-->
<!--                             || route().current()==='certificate.due'-->
<!--                             || route().current()==='certificate.completed'-->
<!--                             || route().current()==='pendency.index'-->
<!--                             || route().current()==='archived-application.index'-->
<!--                             || route().current()==='user-application.certificate'-->
<!--                             || route().current()==='dissolution.create'-->
<!--                    )"/>-->
<!--                        </q-item-section>-->

<!--                        <q-item-section >-->
<!--                            Registration-->
<!--                        </q-item-section>-->

<!--                    </template>-->
<!--                    <template v-for="item in module.registration.children"-->
<!--                              :key="item.route_name">-->
<!--                        <q-item v-if="$page.props.permissions.find(val=>val.name===item.permission)"-->
<!--                                :active="route().current()===item.route_name"-->
<!--                                active-class="active-item text-accent"-->
<!--                                class="nav-item"-->
<!--                                clickable-->
<!--                                @click="$inertia.get(route(item.route_name))">-->
<!--                            <q-item-section avatar class="q-ml-sm">-->
<!--                                <q-icon :size="route().current()===item.route_name ? '12px':'9px'"-->
<!--                                        name="fiber_manual_record"/>-->
<!--                            </q-item-section>-->
<!--                            <q-item-section>-->
<!--                                <q-item-label>{{ item.label }}</q-item-label>-->
<!--                            </q-item-section>-->
<!--                        </q-item>-->
<!--                    </template>-->

<!--                </q-expansion-item>-->
<!--                <q-expansion-item v-if="checkModules('yearly-report')"-->
<!--                                  group="menu"-->
<!--                                  :label="module.yearlyReport.label"-->
<!--                                  :header-class="(-->
<!--                              route().current()==='activity-report.create'-->
<!--                             || route().current()==='activity-report.apply'-->
<!--                             || route().current()==='activity-report.submitted'-->
<!--                             || route().current()==='activity-report.ongoing'-->
<!--                             || route().current()==='activity-report.archived'-->

<!--                             || route().current()==='expenditure-report.create'-->
<!--                             || route().current()==='expenditure-report.apply'-->
<!--                             || route().current()==='expenditure-report.submitted'-->
<!--                             || route().current()==='expenditure-report.ongoing'-->
<!--                             || route().current()==='expenditure-report.archived'-->

<!--                             || route().current()==='member-list.create'-->
<!--                             || route().current()==='member-list.apply'-->
<!--                             || route().current()==='member-list.submitted'-->
<!--                             || route().current()==='member-list.ongoing'-->
<!--                             || route().current()==='member-list.archived'-->
<!--                          )-->

<!--                          ?'active-menu text-accent':''"-->
<!--                                  icon="o_manage_accounts">-->

<!--                    <template #header>-->
<!--                        <q-item-section avatar>-->
<!--                            <ZYearlyReportIcon :active="-->
<!--                             route().current()==='activity-report.create'-->
<!--                             || route().current()==='activity-report.apply'-->
<!--                             || route().current()==='activity-report.submitted'-->
<!--                             || route().current()==='activity-report.ongoing'-->
<!--                             || route().current()==='activity-report.archived'-->

<!--                             || route().current()==='expenditure-report.create'-->
<!--                             || route().current()==='expenditure-report.apply'-->
<!--                             || route().current()==='expenditure-report.submitted'-->
<!--                             || route().current()==='expenditure-report.ongoing'-->
<!--                             || route().current()==='expenditure-report.archived'-->

<!--                             || route().current()==='member-list.create'-->
<!--                             || route().current()==='member-list.apply'-->
<!--                             || route().current()==='member-list.submitted'-->
<!--                             || route().current()==='member-list.ongoing'-->
<!--                             || route().current()==='member-list.archived'-->
<!--                    "/>-->
<!--                        </q-item-section>-->

<!--                        <q-item-section>-->
<!--                            Yearly Report-->
<!--                        </q-item-section>-->
<!--                    </template>-->

<!--                    <template v-for="item in module.yearlyReport.children"-->
<!--                              :key="item.route_name">-->
<!--                        <q-item v-if="$page.props.permissions.find(val=>val.name===item.permission)"-->
<!--                                class="nav-item"-->
<!--                                :active="item.activeRoutes.includes(route().current())"-->
<!--                                active-class="active-item text-accent"-->
<!--                                clickable-->
<!--                                @click="$inertia.get(route(item.route_name))">-->
<!--                            <q-item-section avatar class="q-ml-sm">-->
<!--                                <q-icon :size="item.activeRoutes.includes(route().current()) ? '12px':'9px'"-->
<!--                                        name="fiber_manual_record"/>-->
<!--                            </q-item-section>-->
<!--                            <q-item-section>-->
<!--                                <q-item-label>{{ item.label }}</q-item-label>-->
<!--                            </q-item-section>-->
<!--                        </q-item>-->
<!--                    </template>-->

<!--                </q-expansion-item>-->

<!--                <q-expansion-item v-if="checkModules('payment')"-->
<!--                                  group="menu"-->
<!--                                  :label="module.payment.label"-->
<!--                                  :header-class="(-->
<!--                              route().current()==='payment.verified'-->
<!--                            || route().current()==='payment.unverified'-->
<!--                          )-->
<!--                          ?'active-menu text-accent':''"-->
<!--                                  icon="o_credit_card">-->
<!--                    <template  #header>-->

<!--                        <q-item-section avatar>-->
<!--                            <ZPaymentIcon :active="(-->
<!--                           route().current()==='payment.verified'-->
<!--                            || route().current()==='payment.unverified'-->
<!--                    )"/>-->
<!--                        </q-item-section>-->

<!--                        <q-item-section>-->
<!--                            Payments-->
<!--                        </q-item-section>-->
<!--                    </template>-->
<!--                    <template v-for="item in module.payment.children"-->
<!--                              :key="item.route_name">-->

<!--                        <q-item v-if="$page.props.permissions.find(val=>val.name===item.permission)"-->
<!--                                :active="route().current()===item.route_name"-->
<!--                                active-class="active-item text-accent"-->
<!--                                class="nav-item"-->
<!--                                clickable-->
<!--                                @click="$inertia.get(route(item.route_name))">-->
<!--                            <q-item-section avatar class="q-ml-sm">-->
<!--                                <q-icon :size="route().current()===item.route_name ? '12px':'9px'"-->
<!--                                        name="fiber_manual_record"/>-->
<!--                            </q-item-section>-->
<!--                            <q-item-section>-->
<!--                                <q-item-label>{{ item.label }}</q-item-label>-->
<!--                            </q-item-section>-->
<!--                        </q-item>-->
<!--                    </template>-->
<!--                </q-expansion-item>-->
<!--                <q-expansion-item v-if="checkModules('mis')"-->
<!--                                  group="menu"-->
<!--                                  :label="module.reports.label"-->
<!--                                  :header-class="(-->
<!--                              route().current()==='society.index'-->
<!--                          )-->
<!--                          ?'active-menu text-accent':''"-->
<!--                                  icon="o_credit_card">-->
<!--                    <template  #header>-->
<!--                        <q-item-section avatar>-->
<!--                            <ZMisIcon :active="(-->
<!--                           route().current()==='society.index' ||-->
<!--                           route().current()==='society.show'-->
<!--                    )"/>-->
<!--                        </q-item-section>-->

<!--                        <q-item-section>-->
<!--                            MIS-->
<!--                        </q-item-section>-->
<!--                    </template>-->
<!--                    <template v-for="item in module.reports.children"-->
<!--                              :key="item.route_name">-->

<!--                        <q-item v-if="$page.props.permissions.find(val=>val.name===item.permission)"-->
<!--                                :active="route().current()===item.route_name"-->
<!--                                active-class="active-item text-accent"-->
<!--                                class="nav-item"-->
<!--                                clickable-->
<!--                                @click="$inertia.get(route(item.route_name))">-->
<!--                            <q-item-section avatar class="q-ml-sm">-->
<!--                                <q-icon :size="route().current()===item.route_name ? '12px':'9px'"-->
<!--                                        name="fiber_manual_record"/>-->
<!--                            </q-item-section>-->
<!--                            <q-item-section>-->
<!--                                <q-item-label>{{ item.label }}</q-item-label>-->
<!--                            </q-item-section>-->
<!--                        </q-item>-->
<!--                    </template>-->
<!--                </q-expansion-item>-->
<!--                <q-expansion-item v-if="checkModules('mis')"-->
<!--                                  group="menu"-->
<!--                                  :label="module.requests.label"-->
<!--                                  :header-class="(-->
<!--                              route().current()==='dissolution.submitted' ||-->
<!--                              route().current()==='dissolution.ongoing' ||-->
<!--                              route().current()==='dissolution.archived' ||-->
<!--                              route().current()==='dissolution.show'-->
<!--                          )-->
<!--                          ?'active-menu text-accent':''"-->
<!--                                  icon="o_credit_card">-->
<!--                    <template  #header>-->
<!--                        <q-item-section avatar>-->
<!--                            <ZRequestIcon :active="(-->
<!--                           route().current()==='dissolution.submitted' ||-->
<!--                           route().current()==='dissolution.ongoing' ||-->
<!--                           route().current()==='dissolution.archived' ||-->
<!--                           route().current()==='dissolution.show'-->
<!--                    )"/>-->
<!--                        </q-item-section>-->

<!--                        <q-item-section>-->
<!--                            Requests-->
<!--                        </q-item-section>-->
<!--                    </template>-->
<!--                    <template v-for="item in module.requests.children"-->
<!--                              :key="item.route_name">-->

<!--                        <q-item v-if="$page.props.permissions.find(val=>val.name===item.permission)"-->
<!--                                :active="route().current()===item.route_name"-->
<!--                                active-class="active-item text-accent"-->
<!--                                class="nav-item"-->
<!--                                clickable-->
<!--                                @click="$inertia.get(route(item.route_name))">-->
<!--                            <q-item-section avatar class="q-ml-sm">-->
<!--                                <q-icon :size="route().current()===item.route_name ? '12px':'9px'"-->
<!--                                        name="fiber_manual_record"/>-->
<!--                            </q-item-section>-->
<!--                            <q-item-section>-->
<!--                                <q-item-label>{{ item.label }}</q-item-label>-->
<!--                            </q-item-section>-->
<!--                        </q-item>-->
<!--                    </template>-->
<!--                </q-expansion-item>-->


<!--                <q-separator class="q-my-sm"/>-->
<!--                <q-expansion-item v-if="checkModules('administration')"-->
<!--                                  group="menu"-->
<!--                                  :label="module.admin.label"-->
<!--                                  :header-class="(-->
<!--                              route().current()==='user.index'-->
<!--                             || route().current()==='user.create'-->
<!--                             || route().current()==='user.edit'-->
<!--                             || route().current()==='role.index'-->
<!--                             || route().current()==='role.edit'-->
<!--                             || route().current()==='information.index'-->
<!--                             || route().current()==='information.create'-->
<!--                             || route().current()==='information.edit'-->
<!--                             || route().current()==='certificate.index'-->
<!--                             || route().current()==='district.index'-->
<!--                             || route().current()==='category.index'-->
<!--                             || route().current()==='migration.index'-->
<!--                          )-->

<!--                          ?'active-menu text-accent':''"-->
<!--                                  icon="o_manage_accounts">-->

<!--                    <template #header>-->
<!--                        <q-item-section avatar>-->
<!--                            <ZAdminIcon :active=" route().current()==='user.index'-->
<!--                             || route().current()==='user.create'-->
<!--                             || route().current()==='user.edit'-->
<!--                             || route().current()==='role.index'-->
<!--                             || route().current()==='role.edit'-->
<!--                             || route().current()==='information.index'-->
<!--                             || route().current()==='information.create'-->
<!--                             || route().current()==='information.edit'-->
<!--                             || route().current()==='certificate.index'-->
<!--                             || route().current()==='district.index'-->
<!--                             || route().current()==='category.index'-->
<!--                             || route().current()==='migration.index'-->
<!--                        "/>-->
<!--                        </q-item-section>-->

<!--                        <q-item-section>-->
<!--                            Administration-->
<!--                        </q-item-section>-->
<!--                    </template>-->

<!--                    <template v-for="item in module.admin.children"-->
<!--                              :key="item.route_name">-->
<!--                        <q-item v-if="$page.props.permissions.find(val=>val.name===item.permission)"-->
<!--                                class="nav-item"-->
<!--                                :active="route().current()===item.route_name"-->
<!--                                active-class="active-item text-accent"-->
<!--                                clickable-->
<!--                                @click="$inertia.get(route(item.route_name),{},{preserveState:true})">-->
<!--                            <q-item-section avatar class="q-ml-sm">-->
<!--                                <q-icon :size="route().current()===item.route_name ? '12px':'9px'"-->
<!--                                        name="fiber_manual_record"/>-->
<!--                            </q-item-section>-->
<!--                            <q-item-section>-->
<!--                                <q-item-label>{{ item.label }}</q-item-label>-->
<!--                            </q-item-section>-->
<!--                        </q-item>-->
<!--                    </template>-->

<!--                </q-expansion-item>-->




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
import {reactive,computed,ref} from 'vue'

import {usePage} from "@inertiajs/vue3";
import {useQuasar} from "quasar";


const leftDrawerOpen = ref(false)


const toggleLeftDrawer = () => {
    leftDrawerOpen.value = !leftDrawerOpen.value
}









</script>
