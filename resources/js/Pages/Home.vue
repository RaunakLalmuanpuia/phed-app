<script setup>
import FrontendLayout from "../Layouts/FrontendLayout.vue";

defineOptions({layout:FrontendLayout})
</script>

<template>
    <q-page class="container" padding>
        <div style="background-color: #f2f9ff" class="full-width">
            <div class="container row q-col-gutter-md q-py-lg">
                <div class="col-xs-12 col-sm-7 flex items-center">
                    <div class="column q-gutter-md">
                        <h1 class="hero-title">Manage <span class="text-primary">PHED Employees</span> Efficiently</h1>
                        <p class="home-caption">
                            Access employee records, departmental roles, and device permissions for the PHED department. No registration required. Built for speed and simplicity.
                        </p>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-5">
                    <q-img class="q-pa-none" src="https://img.freepik.com/free-vector/admin-concept-illustration_114360-2143.jpg" />
                </div>
            </div>
        </div>
        <br /><br /><br />

        <div class="container">
            <div  class="row q-col-gutter-md">
                <div class="col-xs-12 col-sm-7">
                    <div style="min-height: 300px" class="stat-card row">
                        <div class="col-xs-12">
                            <h1 class="home-title">System Insights</h1>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <div class="q-mt-md">
                                <h6 class="stat-count">1567</h6>
                                <span class="stat-caption">Employees Listed</span>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <div class="q-mt-md">
                                <h6 class="stat-count">88</h6>
                                <span class="stat-caption">Departments</span>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <p class="stat-footer q-mt-xl">
                                Data is securely managed by the PHED IT team. Updates and access are controlled by internal administration.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-5">
                    <div class="stat-card">
                        <q-img src="https://img.freepik.com/free-vector/data-report-concept-illustration_114360-703.jpg" style="border-radius: 8px" />
                    </div>
                </div>
            </div>
            <br />
            <div class="flex q-gutter-md justify-between items-center q-my-lg">
                <div class="flex items-center">
                    <q-img width="24px" height="24px" src="https://img.icons8.com/ios-filled/50/user-group-man-man.png" />
                    <div class="q-ma-none q-ml-sm text-grey-7 text-bold">Employee Directory</div>
                </div>
                <div class="flex items-center">
                    <q-img width="24px" height="24px" src="https://img.icons8.com/ios-filled/50/task.png" />
                    <p class="q-ma-none q-ml-sm text-grey-7 text-bold">Role Assignment</p>
                </div>
                <div class="flex items-center">
                    <q-img width="24px" height="24px" src="https://img.icons8.com/ios-filled/50/data-encryption.png" />
                    <p class="q-ma-none q-ml-sm text-grey-7 text-bold">Secure Access</p>
                </div>
            </div>

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
                    <q-img width="90%" src="https://img.freepik.com/free-vector/employee-concept-illustration_114360-3545.jpg" />
                </div>
                <div class="col-xs-12 col-sm-8 flex items-center">
                    <div class="column q-gutter-sm">
                        <p class="home-title">Employee Records</p>
                        <p style="max-width: 530px" class="home-caption">
                            Quickly search and view employee details such as name, designation, contact, and posting department.
                        </p>
                    </div>
                </div>
            </div>

            <br /><br /><br />

            <div class="row q-col-gutter-lg">
                <div class="col-xs-12 col-sm-8 flex items-center">
                    <div class="column q-gutter-sm">
                        <p class="home-title">Permission & Device Access</p>
                        <p style="max-width: 530px" class="home-caption">
                            Grant or revoke device-level access to the platform based on employee roles and responsibilities.
                        </p>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-4 text-right">
                    <q-img width="90%" src="https://img.freepik.com/free-vector/office-workers-concept-illustration_114360-1056.jpg" />
                </div>
            </div>

            <br /><br /><br />

            <div class="row q-col-gutter-lg">
                <div class="col-xs-12 col-sm-4">
                    <q-img width="90%" src="https://img.freepik.com/free-vector/data-protection-concept-illustration_114360-629.jpg" />
                </div>
                <div class="col-xs-12 col-sm-8 flex items-center">
                    <div class="column q-gutter-sm">
                        <p class="home-title">Audit & Updates</p>
                        <p style="max-width: 530px" class="home-caption">
                            All updates are logged and monitored. This ensures transparency and accountability across the department.
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
