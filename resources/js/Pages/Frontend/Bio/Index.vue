<template>
    <q-page class="container" >

        <div class="bg-white">
            <div class="row">
                <div class="col-xs-12 col-sm-6">

                    <div class="flex items-center justify-center">

                        <div>
                            <p class="login-title">Employee Bio</p>
                            <q-stepper
                                v-model="step"
                                vertical
                                color="accent"
                                flat
                                animated>
                                <q-step
                                    :name="1"
                                    title="Enter your registered mobile no"
                                    icon="o_email"
                                    :done="step > 1">
                                    <q-form style="max-width: 750px"  class="column q-gutter-sm q-my-lg" @submit="handleSend">

                                        <span class="text-caption text-grey-7 text-weight-medium">Enter your registered mobile no</span>
                                        <q-input placeholder="Mobile"
                                                 dense
                                                 v-model="form.userId"
                                                 outlined
                                                 no-error-icon
                                                 :error="!!form.errors?.userId"
                                                 :error-message="form.errors?.userId?.toString()"
                                                 :rules="[
                                             val=>!!val || 'Mobile is required'
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


                            </q-stepper>

                        </div>

                    </div>

                </div>
                <div v-if="$q.screen.gt.sm" class="col-xs-12 col-sm-6">
                    <q-img height="480" width="580" src="/images/phed_logo.png"/>
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
const handleSend=e=>{
    q.loading.show()
    axios.post(route('bio.send'),{
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
    axios.post(route('bio.verify'),{
        'userId':form.userId,
        ...verifyForm
    })
        .then(res=>{
            const mobile  = res.data.mobile
            if (mobile ) {
                router.visit(route('bio.show', { employee: mobile }))
            } else {
                verifyForm.errors['otp'] = ['Invalid Otp']
            }
        })
        .catch(err=>{
            verifyForm.errors=err?.response?.data?.errors || {}
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

</style>
