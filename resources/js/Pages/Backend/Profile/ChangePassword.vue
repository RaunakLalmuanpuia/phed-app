<template>
    <q-page class="container" padding>
        <div class="flex items-center justify-between q-pa-md bg-white">
            <div>
                <div class="stitle">Profile</div>
                <q-breadcrumbs class="text-dark">
                    <q-breadcrumbs-el class="cursor-pointer"  icon="dashboard" label="Dashboard" @click="$inertia.get(route('dashboard'))"/>
                    <q-breadcrumbs-el class="cursor-pointer" label="Profile" @click="$inertia.get(route('profile.edit'))"/>
                    <q-breadcrumbs-el class="cursor-pointer" label="Go Back" @click="handleBack"/>
                </q-breadcrumbs>
            </div>
        </div>
        <br/>
        <div class="column justify-between items-center">
            <q-form @submit="handleSubmit" style="width: 650px" class="q-pa-md column q-gutter-sm bg-white">
                <q-input v-model="form.old_password"
                         outlined
                         label="Old Password"
                         :error="!!form?.errors?.old_password"
                         :error-message="form?.errors?.old_password?.toString()"
                         no-error-icon
                         :rules="[
                         val=>!!val || 'Old Password is required'
                     ]"
                />

                <q-input v-model="form.password"
                         type="password"
                         outlined
                         label="New Password"
                         :error="!!form?.errors?.password"
                         :error-message="form?.errors?.password?.toString()"
                         no-error-icon
                         :rules="[
                         val=>!!val || 'Password is required',
                         val=>val?.length >6 || 'Password must be greater than 6 character'
                     ]"
                />

                <q-input v-model="form.password_confirmation"
                         type="password"
                         outlined
                         label="Confirm New Password"
                         :error="!!form?.errors?.password_confirmation"
                         :error-message="form?.errors?.password_confirmation?.toString()"
                         no-error-icon
                         :rules="[
                         val=>!!val || 'Confirm Password is required',
                         val=>val === form.password || 'Confirm password does not match password'
                     ]"
                />
                <div class="flex q-gutter-sm">
                    <q-btn type="submit" color="primary" label="Update"/>
                    <q-btn color="negative" outline @click="handleBack" label="Cancel"/>
                </div>
            </q-form>

        </div>
    </q-page>
</template>
<script setup>
import BackendLayout from "@/Layouts/BackendLayout.vue";
import {useForm, usePage} from "@inertiajs/vue3";
import {useQuasar} from "quasar";

defineOptions({
    layout:BackendLayout
})

const q = useQuasar();
const form=useForm({
    old_password:'',
    password:'',
    password_confirmation:''
})

const handleSubmit=e=>{
    form.put(route('profile.update-password'),{
        onStart: params => q.loading.show({
            boxClass: 'bg-grey-2 text-grey-9',
            spinnerColor: 'primary', message: 'Updating Profile...'
        }),
        onFinish:params => q.loading.hide()
    })
}

const handleBack=e=>{
    window.history.back();
}

</script>
