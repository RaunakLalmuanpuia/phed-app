<script setup>
import FrontendLayout from "../Layouts/FrontendLayout.vue";

defineOptions({layout:FrontendLayout})
</script>

<template>
    <q-page class="container" padding>

        <div style="background-color: #f2f9ff" >
            <div class="container row q-col-gutter-md q-py-lg">
                <div class="col-xs-12 col-sm-7 flex items-center">
                    <div class="column q-gutter-md">
                        <h1 class="hero-title">Manage <span class="text-primary">PHED Employees</span> Efficiently</h1>
                        <p class="home-caption">
                            Access employee records, departmental roles, and workplace for the PHED department. Built for speed and simplicity.
                        </p>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-5">

                    <q-img class="q-pa-none" src="images/19209.jpg" />
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
.stat-count{
    font-family: Poppins,serif;
    font-size: 40px;
    font-weight: bold;
    color: #191c51;
}
.stat-caption{
    font-family: Poppins,serif;
    font-size: 16px;
    font-weight: normal;
    color: #515264;
}
.stat-card{
    padding: 20px;
    border: solid 1px #ebebeb;
    background-color: #fff;
}
.stat-footer{
    font-family: Poppins,serif;
    font-size: 16px;
    font-weight: normal;
    color: #9b9b9b;
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
