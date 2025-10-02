<template>
<q-page class="container" padding>
    <div class="flex items-center justify-between q-pa-md bg-white">
        <div>
            <div class="stitle">Edit User</div>
            <q-breadcrumbs class="text-dark">
                <q-breadcrumbs-el class="cursor-pointer"  icon="dashboard" label="Dashboard" @click="$inertia.get(route('dashboard'))"/>
                <q-breadcrumbs-el class="cursor-pointer" label="User Accounts" @click="$inertia.get(route('user.index'))"/>
                <q-breadcrumbs-el class="cursor-pointer" label="Go Back" @click="handleBack"/>
            </q-breadcrumbs>
        </div>
    </div>
    <br/>
    <q-form @submit="submit" class="bg-white q-pa-md">
        <div style="max-width: 720px" class="row q-col-gutter-sm">
            <div class="col-xs-12 col-sm-3">
                <div class="text-label q-mt-md">Name</div>
            </div>
            <div class="col-xs-12 col-sm-9">
                <q-input v-model="form.name"
                         :error="!!form.errors?.name"
                         :error-message="form.errors?.name?.toString()"
                         outlined
                         dense
                         item-aligned
                         :rules="[
                         val=>val !=='' || 'Name is required'
                     ]"/>
            </div>
            <div class="col-xs-12 col-sm-3">
                <div class="text-label q-mt-md">Designation</div>
            </div>
            <div class="col-xs-12 col-sm-9">
                <q-input v-model="form.designation"
                         :error="!!form.errors?.designation"
                         :error-message="form.errors?.designation?.toString()"
                         outlined
                         dense
                         item-aligned
                         />
            </div>
            <div class="col-xs-12 col-sm-3">
                <div class="text-label q-mt-md">Mobile</div>
            </div>
            <div class="col-xs-12 col-sm-9">
                <q-input v-model="form.mobile"
                         mask="##########"
                         :error="!!form.errors?.mobile"
                         :error-message="form.errors?.mobile?.toString()"
                         outlined
                         dense
                         item-aligned
                         :rules="[
                         val=>val !=='' || 'Mobile is required'
                     ]"/>
            </div>
            <div class="col-xs-12 col-sm-3">
                <div class="text-label q-mt-md">Email</div>
            </div>
            <div class="col-xs-12 col-sm-9">
                <q-input v-model="form.email"
                         type="email"
                         :error="!!form.errors?.email"
                         :error-message="form.errors?.email?.toString()"
                         outlined
                         dense
                         item-aligned
                         :rules="[
                         val=>val !=='' || 'Email is required'
                     ]"/>
            </div>
            <div class="col-xs-12 col-sm-3">
                <div class="text-label q-mt-md">Role</div>
            </div>
            <div class="col-xs-12 col-sm-9">
                <q-select v-model="form.roles"
                         :error="!!form.errors?.roles"
                          :options="userRoles"
                          multiple
                          use-chips
                         :error-message="form.errors?.roles?.toString()"
                         outlined
                         dense
                         item-aligned
                />
            </div>

            <template v-if="showOffice">
                <div class="col-xs-12 col-sm-3">
                    <div class="text-label q-mt-md">Office</div>
                </div>
                <div class="col-xs-12 col-sm-9">
                    <q-select v-model="form.offices"
                              :error="!!form.errors?.office_id"
                              :error-message="form.errors?.office_id?.toString()"
                              :options="offices"
                              :rules="[
                             val=>!!val || 'Office is required'
                         ]"
                              multiple
                              use-chips
                              no-error-icon
                              outlined
                              item-aligned
                    />
                </div>
            </template>
            <div class="col-xs-12 col-sm-3">
                <div class="text-label q-mt-md">Password</div>
            </div>
            <div class="col-xs-12 col-sm-9">
                <q-input v-model="form.password"
                         type="password"
                         :error="!!form.errors?.password"
                         :error-message="form.errors?.password?.toString()"
                         outlined
                         dense
                         item-aligned/>
            </div>
            <div class="col-xs-12 col-sm-3">
                <div class="text-label q-mt-md">Password Confirmation</div>
            </div>
            <div class="col-xs-12 col-sm-9">
                <q-input v-model="form.password_confirmation"
                         type="password"
                         :error="!!form.errors?.password_confirmation"
                         :error-message="form.errors?.password_confirmation?.toString()"
                         outlined
                         dense
                         item-aligned/>
            </div>
            <div class="col-xs-12">
                <q-separator class="q-my-md"/>
            </div>
            <div class="col-xs-12 flex q-gutter-sm">
                <q-btn class="sized-btn" type="submit" color="btn-primary" label="Update"/>
                <q-btn class="sized-btn" color="negative" outline label="Cancel" @click="$inertia.get(route('user.index'))"/>
            </div>

        </div>
    </q-form>

</q-page>
</template>
<script setup>
import BackendLayout from "@/Layouts/BackendLayout.vue";
import {useForm} from "@inertiajs/vue3";
import {useQuasar} from "quasar";
import {computed} from "vue";

defineOptions({layout:BackendLayout})
const props=defineProps(['userRoles','data','current_offices','offices']);
const q = useQuasar();
const form=useForm({
    name:props.data?.name,
    email:props?.data?.email,
    mobile:props?.data?.mobile,
    roles: props?.data?.roles?.map(item=>({value:item.name,label:item.name})),
    offices:props.current_offices,
    designation:props?.data.designation,
    password:'',
    password_confirmation:'',

})
const showOffice = computed(() =>
    form.roles.some(r => r.value === 'Manager' || r.value === 'Viewer' )
);
// const submit=e=>{
//     form.transform(data => ({...data,
//         office_ids: data?.offices?.map(item=>item.value),
//         roles:data.roles.map(item=>item.value)}))
//     .put(route('user.update',props.data.id),{
//         onStart:params => q.loading.show(),
//         onFinish:params => q.loading.hide()
//     })
// }

const submit = e => {
    form.transform(data => {
        const hasManagerOrViewer = data.roles.some(
            r => r.value === 'Manager' || r.value === 'Viewer'
        )

        return {
            ...data,
            office_ids: hasManagerOrViewer
                ? data?.offices?.map(item => item.value)
                : null,   // or [] if you want to clear offices
            roles: data.roles.map(item => item.value)
        }
    })
        .put(route('user.update', props.data.id), {
            onStart: () => q.loading.show(),
            onFinish: () => q.loading.hide()
        })
}
const handleBack=e=>{
    window.history.back();
}
</script>
