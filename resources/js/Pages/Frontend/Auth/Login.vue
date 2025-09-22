<template>
    <q-page class="container" >
        <div class="bg-white">
            <div class="row ">

                <div v-if="$q.screen.gt.sm" class="col-xs-12 col-sm-6">
                    <q-img height="480" width="580" style="height: 600px;" src="images/login.png"/>
                </div>

                <div class="col-xs-12 col-sm-6 flex items-center justify-center"
                     :class="{ 'form-bg': $q.screen.gt.sm }">
                    <div >
                        <div v-if="!!$page.props.auth?.user" class="column q-gutter-sm q-my-lg q-pa-lg rounded-lg shadow-2 bg-gray-100 text-gray-900">
                            <p class="text-dark text-center">You are logged in</p>
                            <q-btn @click="$inertia.get(route('dashboard'))" flat label="Go to dashboard"/>
                        </div>
                        <q-form
                            style="max-width: 450px"
                            v-else
                            class="column q-gutter-sm q-my-lg q-pa-lg rounded-lg shadow-2 bg-gray-100 text-gray-900"
                            @submit="handleSubmit"
                        >
                            <p class="login-title text-xl font-semibold">Employees Database Management System</p>

                            <label class="text-gray-700 font-medium" for="email">Email</label>
                            <q-input
                                id="email"
                                v-model="form.email"
                                dense
                                outlined
                                hide-bottom-space
                                no-error-icon
                                :error="!!form.errors?.email"
                                :error-message="form.errors?.email"
                                :rules="[ val => !!val || 'Email is required' ]"
                            />

                            <label class="text-gray-700 font-medium" for="password">Password</label>
                            <q-input
                                :type="state.visiblePassword ? 'text' : 'password'"
                                v-model="form.password"
                                outlined
                                dense
                                no-error-icon
                                hide-bottom-space
                                :error="!!form.errors?.password"
                                :error-message="form.errors?.password"
                                :rules="[ val => !!val || 'Password is required' ]"
                            >
                                <template #append>
                                    <q-icon
                                        @click="state.visiblePassword = !state.visiblePassword"
                                        :name="state.visiblePassword ? 'visibility' : 'visibility_off'"
                                    />
                                </template>
                            </q-input>

                            <div class="flex justify-end">
                                <a
                                    :href="route('login.forgot')"
                                    class="text-blue-600 font-medium cursor-pointer hover:underline"
                                >
                                    Forgot Password ?
                                </a>
                            </div>

                            <div class="flex">
                                <q-btn
                                    class="sized-btn"
                                    color="primary"
                                    type="submit"
                                    rounded
                                    label="Login"
                                    no-caps
                                />
                            </div>
                        </q-form>

                    </div>

                </div>

            </div>

        </div>
    </q-page>
</template>
<script setup>
import FrontendLayout from "@/Layouts/FrontendLayout.vue";
import {useForm} from "@inertiajs/vue3";
import {useQuasar} from "quasar";
import {reactive} from "vue";
defineOptions({layout:FrontendLayout})

const q = useQuasar();
const state=reactive({
    visiblePassword:false
})
const form=useForm({
    email:'',
    password:''
})
const handleSubmit=e=>{
    form.post(route('login.store'),{
        onStart:params => q.loading.show(),
        onFinish:params => q.loading.hide()
    })
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

.form-bg {
    background: url('/images/river.jpg') no-repeat center center;
    background-size: cover;
}
</style>
