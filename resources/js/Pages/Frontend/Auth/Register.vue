<template>
    <q-page class="container" >

        <div class="bg-white  q-mt-xl">
            <div class="row ">
                <div class="col-xs-12 col-sm-6">
                    <div v-if="!!$page.props.auth?.user" class="column q-gutter-sm">
                        <p class="text-dark">You are logged in</p>
                        <q-btn @click="$inertia.get(route('dashboard'))" flat label="Go to dashboard"/>
                    </div>
                    <div v-else class="column q-gutter-xs register-card justify-center items-center">
                        <div v-if="state.sentOtp">
                            <q-form @submit="handleOtp" class="column ">
                                <p class="login-title text-left">OTP</p>
                                <q-input label="Mobile OTP"
                                         v-model="otpForm.mobile_otp"
                                         outlined
                                         dense
                                         mask="####"
                                         no-error-icon
                                         :error="!!otpForm.errors?.mobile_otp"
                                         :error-message="otpForm.errors?.mobile_otp?.toString()"
                                         :rules="[
                                            val=>!!val || 'Mobile OTP is required'
                                        ]"/>
                                <div class="text-primary text-bold cursor-pointer q-mb-lg text-left">Resend OTP</div>
                                <q-input label="Email OTP"
                                         v-model="otpForm.email_otp"
                                         outlined
                                         dense
                                         mask="####"
                                         no-error-icon
                                         :error="!!otpForm.errors?.email_otp"
                                         :error-message="otpForm.errors?.email_otp?.toString()"
                                         :rules="[
                                            val=>!!val || 'Email OTP is required'
                                        ]"/>
                                <div class="text-primary text-bold cursor-pointer q-mb-lg text-left">Resend Email OTP</div>
                                <div class="flex">
                                    <q-btn class="sized-btn" color="btn-primary" type="submit" rounded label="Confirm" no-caps/>
                                </div>

                            </q-form>
                        </div>
                        <q-form style="max-width: 420px" @submit="handleSubmit" v-else>
                            <div class="text-left">
                                <p class="login-title">Sign up</p>
                            </div>
                            <q-input label="Name"
                                     v-model="form.name"
                                     outlined
                                     no-error-icon
                                     :error="!!form.errors?.name"
                                     :error-message="form.errors?.name?.toString()"
                                     :rules="[
                                 val=>!!val || 'Name is required'
                             ]"/>
                            <q-input label="Phone Number"
                                     v-model="form.mobile"
                                     mask="##########"
                                     outlined
                                     no-error-icon
                                     :error="!!form.errors?.mobile"
                                     :error-message="form.errors?.mobile?.toString()"
                                     :rules="[
                                    val=>!!val || 'Phone Number is required',
                                ]"/>
                            <q-input label="Email"
                                     v-model="form.email"
                                     outlined
                                     no-error-icon
                                     :error="!!form.errors?.email"
                                     :error-message="form.errors?.email?.toString()"
                                     :rules="[
                                 val=>!!val || 'Email is required'
                             ]"/>
                            <q-input label="Password"
                                     v-model="form.password"
                                     :type="state.visiblePassword?'text':'password'"
                                     outlined
                                     no-error-icon
                                     :error="!!form.errors?.password"
                                     :error-message="form.errors?.password?.toString()"
                                     :rules="[
                                 val=>!!val || 'Password is required'
                             ]"
                            >
                                <template #append>
                                    <q-icon @click="state.visiblePassword=!state.visiblePassword" :name="state.visiblePassword?'visibility':'visibility_off'"/>
                                </template>
                            </q-input>
                            <q-input label="Confirm Password"
                                     :type="state.visiblePassword?'text':'password'"
                                     v-model="form.password_confirmation"
                                     outlined
                                     no-error-icon
                                     :error="!!form.errors?.password_confirmation"
                                     :error-message="form.errors?.password_confirmation?.toString()"
                                     :rules="[
                                 val=>!!val || 'Confirm Password is required',
                                 val=>val===form.password || 'Confirm password does not match password'
                             ]"
                            />
                            <div class="flex q-mt-sm">
                                <q-btn class="sized-btn" color="btn-primary" type="submit" rounded label="Next" no-caps/>
                            </div>
                        </q-form>
                    </div>


                </div>
                <div v-if="$q.screen.gt.sm" class="col-xs-12 col-sm-6">
                    <q-img height="480" width="580" src="images/register.jpg"/>
                </div>
            </div>

        </div>
    </q-page>
</template>
<script setup>
import FrontendLayout from "@/Layouts/FrontendLayout.vue";
import {router, useForm} from "@inertiajs/vue3";
import {useQuasar} from "quasar";
import {reactive} from "vue";
defineOptions({layout:FrontendLayout})

const q = useQuasar();
const state=reactive({
    sentOtp:false,
    visiblePassword:false
})
const form=useForm({
    name: '',
    mobile: '',
    email:'',
    password:'',
    password_confirmation:''
})
const otpForm=useForm({
    mobile_otp: '',
    email_otp:'',
})
const handleOtp=e=>{

    otpForm.transform(data => ({...data,...form.data()}))
        .post(route('register.confirm-otp'),{
        onStart:params =>  q.loading.show({
            boxClass: 'bg-grey-2 text-grey-9',
            spinnerColor: 'primary', message: ' Confirming...'
        }),
        onSuccess:params => {
            state.sentOtp = false;
        },
        onFinish:params => q.loading.hide()
    })
}
const handleSubmit=e=>{
    q.loading.show({
        boxClass: 'bg-grey-2 text-grey-9',
        spinnerColor: 'primary', message: ' Sending OTP...'
    })
    axios.post(route('register.send-otp'),form.data())
    .then(res=>{
        if (res.data.status) {
            state.sentOtp = true;
        }
        form.setError({})
    })
    .catch(err=>{
        state.sentOtp = false;
        if (err?.response?.data?.errors) {
            form.setError(err.response.data.errors);
        }
        q.notify({type:'negative',message:err?.response?.data?.message || err.toString()})
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
.register-card{
    padding: 32px;
    text-align: center;
}
@media (max-width: 599px) {
    .register-card{
        padding: 12px;
    }
}
</style>
