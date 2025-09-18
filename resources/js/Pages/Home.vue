<script setup>
import FrontendLayout from "../Layouts/FrontendLayout.vue";
import { ref } from "vue";
defineOptions({layout:FrontendLayout})
const slide = ref(1);
</script>

<template>
    <q-page class="container" padding>

        <div style="background-color: #f2f9ff" >
            <div class="container row q-col-gutter-md q-py-lg">
                <div class="col-xs-12 col-sm-5 flex items-center">
                    <div class="column q-gutter-md">

                        <div class="hero-title">Employees Database Management System</div>
                        <p class="home-caption">
                            Access employee records, departmental roles, and workplace for the PHED department. Built for speed and simplicity.
                        </p>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-7">
                    <q-carousel
                        v-model="slide"
                        swipeable
                        animated
                        arrows
                        navigation
                        infinite
                        autoplay
                        interval="3000"
                        class="rounded-borders"
                    >
                        <q-carousel-slide :name="1" img-src="images/Assets/1.jpg" />
                        <q-carousel-slide :name="2" img-src="images/Assets/2.jpg" />
                        <q-carousel-slide :name="3" img-src="images/Assets/3.jpg" />
                        <q-carousel-slide :name="4" img-src="images/Assets/4.jpg" />
                        <q-carousel-slide :name="5" img-src="images/Assets/5.jpg" />
                        <q-carousel-slide :name="6" img-src="images/Assets/6.jpg" />
                        <q-carousel-slide :name="7" img-src="images/Assets/7.jpg" />
                        <q-carousel-slide :name="8" img-src="images/Assets/8.jpg" />
                        <q-carousel-slide :name="9" img-src="images/Assets/9.jpg" />
                    </q-carousel>
                </div>
            </div>
        </div>
        <br /><br /><br />

        <div class="container">
            <br />
            <div id="feature" class="feature">
                <div class="flex justify-center items-center">
                    <div class="text-center">
                        <div class="feature-title">Features</div>
                        <div class="home-line"/>
                    </div>

                </div>
            </div>
            <div class="row q-col-gutter-lg">
                <div class="col-xs-12 col-sm-4">
                    <q-img width="90%" src="images/4565.jpg" />
                </div>
                <div class="col-xs-12 col-sm-8 flex items-center">
                    <div class="column q-gutter-sm">
                        <p class="home-title">Employee Records</p>
                        <p style="max-width: 530px" class="home-caption">
                            Quickly search and view employee details such as name, designation, contact, and posting office.
                        </p>
                    </div>
                </div>
            </div>

            <br /><br /><br />

            <div class="row q-col-gutter-lg">
                <div class="col-xs-12 col-sm-8 flex items-center">
                    <div class="column q-gutter-sm">

                        <p class="home-title">Audit & Updates</p>
                        <p style="max-width: 530px" class="home-caption">
                            All updates are logged and monitored. This ensures transparency and accountability across the offices.
                        </p>

                    </div>
                </div>
                <div class="col-xs-12 col-sm-4 text-right">
                    <q-img width="90%" src="images/4151017.jpg" />
                </div>
            </div>

            <br /><br /><br />

            <div class="row q-col-gutter-lg">
                <div class="col-xs-12 col-sm-4">
                    <q-img width="90%"  src="images/432150.jpg"  />
                </div>
                <div class="col-xs-12 col-sm-8 flex items-center">
                    <div class="column q-gutter-sm">
                        <p class="home-title">Permission & Account Access</p>
                        <p style="max-width: 530px" class="home-caption">
                            Grant or revoke account-level access to the platform based on users roles and permissions.
                        </p>
                    </div>
                </div>
            </div>

            <br /><br /><br />


        </div>
    </q-page>
</template>


<style scoped>

.home-title {
    font-family: Poppins, serif;
    font-size: 28px;
    font-weight: bold;
    color: #191c51;
}

.home-caption {
    font-family: Poppins, serif;
    font-size: 16px;
    font-weight: normal;
    text-align: left;
    color: #515264;
}
.hero-title{
    font-family: Poppins,serif;
    font-size: 48px;
    font-weight: bold;
    line-height: 1.2;
    color: #191c51;
    margin-left: 20px;
}
.feature-title{

    color: #191C51;
    font-weight: bold;
    font-size: 35px;
}
.home-line{
    background-color: #1DEFFF;
    height:5px;
    width: 150px;
}
.feature{
    color: #1ED9E8;
    font-size: 20px;
    font-weight: bold;
}
</style>
