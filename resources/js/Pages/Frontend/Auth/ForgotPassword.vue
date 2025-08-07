<template>
    <q-page class="container" >

        <div class="bg-white  q-mt-xl">
            <div class="row ">
                <div class="col-xs-12 col-sm-6">

                    <div class="flex items-center justify-center">
                        <div v-if="!!$page.props.auth?.user" class="column q-gutter-sm">
                            <p class="text-dark">You are logged in</p>
                            <q-btn @click="$inertia.get(route('dashboard'))" flat label="Go to dashboard"/>
                        </div>

                        <div>
                            <p class="login-title">Forgot Password</p>
                            <q-stepper
                                v-model="step"
                                vertical
                                color="accent"
                                flat
                                animated>
                                <q-step
                                    :name="1"
                                    title="Enter your registered email/mobile no"
                                    icon="o_email"
                                    :done="step > 1">
                                    <q-form style="max-width: 750px"  class="column q-gutter-sm q-my-lg" @submit="handleSend">

                                        <span class="text-caption text-grey-7 text-weight-medium">Enter your registered email or mobile no</span>
                                        <q-input placeholder="Email/Mobile"
                                                 dense
                                                 v-model="form.userId"
                                                 outlined
                                                 no-error-icon
                                                 :error="!!form.errors?.userId"
                                                 :error-message="form.errors?.userId?.toString()"
                                                 :rules="[
                                 val=>!!val || 'Email/Mobile is required'
                             ]"/>

                                        <div class="flex">
                                            <q-btn class="sized-btn" color="btn-primary" type="submit" rounded label="Send OTP" no-caps/>
                                        </div>
                                    </q-form>
                                </q-step>
                                <q-step
                                    :name="2"
                                    title="Verify OTP"
                                    icon="o_verified"
                                    :done="step > 2"
                                >
                                    <q-form style="max-width: 750px"  class="column q-gutter-sm q-my-lg" @submit="handleVerify">

                                        <span class="text-caption text-grey-7 text-weight-medium">Verify OTP</span>
                                        <q-input placeholder="OTP"
                                                 dense
                                                 v-model="verifyForm.otp"
                                                 outlined
                                                 no-error-icon
                                                 :error="!!verifyForm.errors?.otp"
                                                 :error-message="verifyForm.errors?.otp?.toString()"
                                                 :rules="[
                                 val=>!!val || 'OTP is required'
                             ]"/>

                                        <div class="flex">
                                            <q-btn class="sized-btn" color="btn-primary" type="submit" rounded label="Verify OTP" no-caps/>
                                        </div>
                                    </q-form>
                                </q-step>
                                <q-step
                                    :name="3"
                                    title="New Password"
                                    icon="o_password"
                                    :done="step > 3">
                                    <q-form style="max-width: 750px"  class="column q-gutter-sm q-my-lg" @submit="handleConfirm">

                                        <span class="text-caption text-grey-7 text-weight-medium">Set Password</span>
                                        <q-input placeholder="New Password"
                                                 type="password"
                                                 dense
                                                 v-model="resetForm.password"
                                                 outlined
                                                 no-error-icon
                                                 :error="!!resetForm.errors?.password"
                                                 :error-message="resetForm.errors?.password"
                                                 :rules="[
                                 val=>!!val || 'Email/Mobile is required'
                             ]"/>
                                        <q-input placeholder="Confirm Password"
                                                 type="password"
                                                 dense
                                                 v-model="resetForm.password_confirmation"
                                                 outlined
                                                 no-error-icon
                                                 :error="!!resetForm.errors?.password_confirmation"
                                                 :error-message="resetForm.errors?.password_confirmation"
                                                 :rules="[
                                 val=>!!val || 'Confirm Password is required',
                                 val=>val===resetForm.password || 'Confirm Password does not match New password',
                             ]"/>

                                        <div class="flex">
                                            <q-btn class="sized-btn" color="btn-primary" type="submit" rounded label="Confirm" no-caps/>
                                        </div>
                                    </q-form>

                                </q-step>

                            </q-stepper>

                        </div>

                    </div>

                </div>
                <div class="col-xs-12 col-sm-6">
                    <q-img height="480" width="580" src="images/20944201.jpg"/>
                </div>
            </div>

        </div>
    </q-page>
</template>
<script setup>
import FrontendLayout from "@/Layouts/FrontendLayout.vue";
import {router, useForm} from "@inertiajs/vue3";
import {useQuasar} from "quasar";
import {reactive, ref} from "vue";
defineOptions({layout:FrontendLayout})

const q = useQuasar();
const step = ref(1)
const form=reactive({
    userId:'',
    errors:{}
})
const verifyForm=reactive({
    otp:'',
    errors:{}
})
const resetForm=reactive({
    password:'',
    password_confirmation:'',
    errors:{}
})
const handleSend=e=>{
    q.loading.show()
    axios.post(route('forgot.send'),{
        ...form
    })
    .then(res=>{
        step.value = 2;
    })
    .catch(err=>{
        form.errors=err?.response?.data?.errors || {}
    })
    .finally(()=>q.loading.hide())
}
const handleVerify=e=>{
    q.loading.show()
    axios.post(route('forgot.verify'),{
        'userId':form.userId,
        ...verifyForm
    })
        .then(res=>{
            const {data} = res.data;
            if (data) {
                step.value = 3;
            }else{
                verifyForm.errors['otp']=['Invalid Otp']
            }
        })
        .catch(err=>{
            verifyForm.errors=err?.response?.data?.errors || {}
        })
        .finally(()=>q.loading.hide())
}
const handleConfirm=e=>{
    q.loading.show()
    axios.post(route('forgot.password'),{
        ...resetForm,
        userId: form.userId
    }).then(res=>{
            const {message} = res.data;
            q.notify({
                type:'positive',
                message
            })
            router.get(route('login'))
        })
        .catch(err=>{
            resetForm.errors=err?.response?.data?.errors || {}
        })
        .finally(()=>q.loading.hide())
}

</script>
<style scoped>
.login-title{
    padding: 0;
    margin-left: 8px;
    color: #191c51;
    font-size: 28px;
    font-weight: bold;
}
.signup{
    font-family: Roboto,serif;
    font-size: 16px;
    font-weight: normal;
    color: #080808;
}
</style>
