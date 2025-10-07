<template>
    <q-layout @scroll="handleScroll" style="background: #ffffff" view="hHh lpR fff">

        <q-header height-hint="90"  :class="classObject">
            <q-toolbar  class="container flex justify-between items-center" style="height: 80px">
                <div class="flex items-center q-gutter-sm cursor-pointer" @click="$inertia.get(route('home'))">
                    <q-img width="60px" src="/images/phed_logo.png" />
                    <p v-if="$q.screen.gt.sm" class="text-primary">PUBLIC HEALTH ENGINEERING DEPARTMENT : MIZORAM</p>
                </div>

                <div class="flex items-center q-gutter-md">
                    <a v-if="$q.screen.gt.sm && $page.props.auth?.user"  class="nav-link" :href="route('dashboard')">Dashboard</a>
                    <q-btn v-if="!$page.props.auth?.user" square @click="$inertia.get(route('bio.index'))" class="login-btn" outline label="View Bio" no-caps/>
                    <q-btn v-if="!$page.props.auth?.user" square @click="$inertia.get(route('login'))" class="login-btn" outline label="Login" no-caps/>
                    <q-btn v-if="$page.props.auth?.user" square @click="$inertia.delete(route('login.destroy'))" class="login-btn" outline label="Logout" no-caps/>
                </div>
            </q-toolbar>
        </q-header>

        <q-page-container>
            <slot />
        </q-page-container>

        <q-footer  class="bg-secondary text-footer q-py-md">
            <q-toolbar class="container flex justify-between items-center">
                <div class="flex q-gutter-sm items-center">
                    <img src="/images/msegs.png"/>
                    <div>
                        <div>Crafted with care by</div>
                        <div>Mizoram State e-Governance Society</div>
                    </div>
                </div>



                <div class="column items-end">
                    <div>© 2025 EDMS-PHED (GoM). All rights reserved</div>
                    <div class="flex q-gutter-sm">
                        <a class="footer-link" :href="route('page.contact')">Contact Us</a>
                        <a class="footer-link" :href="route('page.privacy')">Privacy Policy</a>
                        <a class="footer-link" :href="route('page.term')">Terms and condition</a>
                    </div>
                </div>


            </q-toolbar>
        </q-footer>

    </q-layout>
</template>
<script setup>

import {computed, reactive} from "vue";

const state=reactive({
    isTop:true
})
const classObject=computed(()=>({
    'bg-transparent':state.isTop,
    'text-primary':state.isTop,
    'bg-white shadow-bottom-5 text-primary':!state.isTop,
}))

const handleScroll=detail=>{
    const {position} = detail;
    if (position < 10) {
        state.isTop=true
    }else {
        state.isTop=false
    }
}


</script>
<style scoped>

.login-btn{
    min-width: 100px;
}
.nav-link{
    text-decoration: none;
    width: 80px;
    color: #444444;
}
.text-footer{
    color: #686B9A;
}

.footer-link{
    text-decoration: none;
    color: #686B9A;
    font-size: 12px;
}
</style>
