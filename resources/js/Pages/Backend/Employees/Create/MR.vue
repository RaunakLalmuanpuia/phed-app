<template>
    <q-page class="container" padding>
        <div class="flex items-center justify-between q-pa-md bg-white">
            <div>
                <div class="stitle">Create Employee - Muster Roll</div>
                <q-breadcrumbs class="text-dark">
                    <q-breadcrumbs-el class="cursor-pointer"  icon="dashboard" label="Dashboard" @click="$inertia.get(route('dashboard'))"/>
                    <q-breadcrumbs-el class="cursor-pointer" label="All Employees" @click="$inertia.get(route('employees.all'))"/>
                    <q-breadcrumbs-el class="cursor-pointer" label="Go Back" @click="goBack"/>
                </q-breadcrumbs>
            </div>
        </div>
        <br/>
        <q-form @submit.prevent="submit" class="bg-white q-pa-md">
            <q-stepper v-model="step" ref="stepper" animated color="secondary">
                <!-- Step 1: Personal Info -->
                <q-step name="1" title="Personal Info" icon="person" :done="step > 1">

                    <div class="q-pa-md">
                        <div class="row items-center q-gutter-md">
                            <!-- Profile Image Preview -->
                            <div class="rounded-borders overflow-hidden" style="background-color: #8a82f7;">
                                <q-img
                                    :src="previewUrl || defaultUrl"
                                    alt="Profile Photo"
                                    style="width: 120px; height: 120px"
                                    img-class="object-cover"
                                />
                            </div>

                            <!-- Buttons & Info -->
                            <div class="column q-gutter-sm">
                                <div class="row q-gutter-md">
                                    <q-btn
                                        label="Upload new Photo"
                                        color="primary"
                                        @click="triggerFileInput"
                                        class="text-weight-medium"
                                        rounded
                                        unelevated
                                    />
                                    <q-btn
                                        label="Reset"
                                        color="grey-4"
                                        text-color="dark"
                                        @click="resetPhoto"
                                        class="text-weight-medium"
                                        rounded
                                        unelevated
                                    />
                                </div>
                                <p class="text-subtitle2 text-grey-7">
                                    Allowed JPG, GIF or PNG. Max size of 800K
                                </p>
                            </div>
                        </div>

                        <!-- Hidden file input -->
                        <input
                            ref="fileInput"
                            type="file"
                            accept="image/*"
                            class="hidden"
                            @change="handleFileChange"
                        />
                    </div>

                    <div class="row q-col-gutter-sm">
                        <div class="col-12 col-sm-6">
                            <q-input v-model="form.name" label="Name *" outlined dense :error="!!form.errors?.name"
                                     :error-message="form.errors?.name" :rules="[val => !!val || 'Name is required']" />
                        </div>
                        <div class="col-12 col-sm-6">
                            <q-input v-model="form.email" label="Email" type="email" outlined dense
                                     :error="!!form.errors?.email" :error-message="form.errors?.email" />
                        </div>
                        <div class="col-12 col-sm-6">
                            <q-input v-model="form.mobile" label="Mobile *" mask="##########" outlined dense
                                     :error="!!form.errors?.mobile" :error-message="form.errors?.mobile"
                                     :rules="[val => !!val || 'Mobile is required']" />
                        </div>
                        <div class="col-12 col-sm-6">
                            <q-input v-model="form.parent_name" label="Parent Name *" outlined dense
                                     :error="!!form.errors?.parent_name" :error-message="form.errors?.parent_name"
                                     :rules="[val => !!val || 'Parent Name is required']" />
                        </div>
                        <div class="col-12 col-sm-6">
                            <q-input v-model="form.date_of_birth" label="Date of Birth *" type="date" outlined dense
                                     :error="!!form.errors?.date_of_birth" :error-message="form.errors?.date_of_birth"
                                     :rules="[val => !!val || 'Date of Birth is required']" />
                        </div>
                        <div class="col-12 col-sm-6">
                            <q-input v-model="form.address" label="Address" outlined dense
                                     :error="!!form.errors?.address" :error-message="form.errors?.address"/>
                        </div>
                        <div class="col-12 col-sm-6">
                            <q-select v-model="form.educational_qln" :options="educationalQualifications" label="Educational Qualification *" outlined dense
                                      emit-value map-options :error="!!form.errors?.educational_qln" :error-message="form.errors?.educational_qln"
                                      :rules="[val => !!val || 'Education Qualification is required']" />
                        </div>
                        <div class="col-12 col-sm-6">
                            <q-input v-model="form.technical_qln" label="Technical Qualification" outlined dense
                                     :error="!!form.errors?.technical_qln" :error-message="form.errors?.technical_qln"
                            />
                        </div>
                    </div>
                    <q-stepper-navigation>
                        <q-btn flat color="negative" label="Cancel" class="q-ml-sm" @click="$inertia.get( route('employees.all'))"  />
                        <q-btn color="primary" label="Next" @click="nextStep" />
                    </q-stepper-navigation>
                </q-step>

                <!-- Step 2: Job Info -->
                <q-step name="2" title="Job Info" icon="work" :done="step > 2">
                    <div class="row q-col-gutter-sm">
                        <div class="col-12 col-sm-6">
                            <q-input v-model="form.post_assigned" label="Post/Work Assigned *" outlined dense
                                     :error="!!form.errors?.post_assigned" :error-message="form.errors?.post_assigned"
                                     />
                        </div>
                        <div class="col-12 col-sm-6">
                            <q-select v-model="form.office" label="Office *" :options="offices" option-label="name" option-value="id"
                                      emit-value map-options outlined dense
                                      :error="!!form.errors?.office" :error-message="form.errors?.office"
                                      :rules="[val => !!val || 'Office is required']" />
                        </div>
                        <div class="col-12 col-sm-6">
                            <q-input v-model="form.date_of_engagement" label="Date of Initial Engagement *" type="date" outlined dense
                                     :error="!!form.errors?.date_of_engagement" :error-message="form.errors?.date_of_engagement"
                                     :rules="[val => !!val || 'Date of Initial Engagement is required']"
                            />
                        </div>
                        <div class="col-12 col-sm-6">
                            <q-select  v-model="form.skill_category" label="Skill Category at Initial Engagement *" :options="skills"
                                       emit-value map-options outlined dense
                                       :error="!!form.errors?.skill_category" :error-message="form.errors?.skill_category"
                                       :rules="[val => !!val || 'Skill Category at Initial Engagement is required']" />
                        </div>
                        <div class="col-12 col-sm-6">
                            <q-select v-model="form.skill_at_present" label="Skill at Present *" :options="skills"
                                      emit-value map-options outlined dense
                                      :error="!!form.errors?.skill_at_present" :error-message="form.errors?.skill_at_present"
                                      :rules="[val => !!val || 'Skill at Present is required']" />
                        </div>
                        <div class="col-12 col-sm-6">
                            <q-input v-model="form.post_per_qualification" label="Post Selected" outlined dense
                                     :error="!!form.errors?.post_per_qualification" :error-message="form.errors?.post_per_qualification"
                            />
                        </div>

                        <div class="col-12 col-sm-6">
                            <q-input v-model="form.name_of_workplace" label="Workplace" outlined dense
                                     :error="!!form.errors?.name_of_workplace" :error-message="form.errors?.name_of_workplace"
                                      />
                        </div>

                        <div class="col-12 col-sm-6">
                            <!-- Toggle: Scheme or not -->
                            <q-select
                                v-model="isScheme"
                                :options="[
                                  { label: 'Yes', value: true },
                                  { label: 'No', value: false }
                                ]"
                                label="Does Employee belong to a Scheme? *"
                                outlined
                                dense
                                emit-value
                                map-options
                            />
                        </div>

                        <div v-if="isScheme" class="col-12 col-sm-6">
                            <q-select v-model="form.scheme" label="Scheme *" :options="schemes" option-label="name" option-value="id"
                                      emit-value map-options outlined dense
                                      :error="!!form.errors?.scheme" :error-message="form.errors?.scheme"
                                      :rules="[val => !!val || 'Scheme is required']" />
                        </div>






                    </div>
                    <q-stepper-navigation>
                        <q-btn flat color="negative" label="Cancel" class="q-ml-sm" @click="$inertia.get( route('employees.all'))"   />
                        <q-btn flat color="primary" label="Back" @click="prevStep" class="q-ml-sm" />
                        <q-btn color="primary" label="Next" @click="nextStep"  class="q-ml-sm" />

                    </q-stepper-navigation>
                </q-step>

                <!--                Step 3: Document upload-->
                <q-step
                    name="3"
                    title="Upload Documents"
                    icon="cloud_upload"
                    :done="step > 3"
                >
                    <div class="row q-col-gutter-sm">
                        <div
                            class="col-12 col-sm-6"
                            v-for="(type, index) in documentTypes"
                            :key="type.id"
                        >
                            <div class="text-subtitle2 q-mb-xs">{{ type.name }}</div>
                            <q-file
                                filled
                                v-model="form.documents[type.id]"
                                label="Upload File"
                                accept=".pdf,.jpg,.jpeg,.png"
                                :counter="true"
                                clearable
                                class="full-width"
                            />
                        </div>
                    </div>

                    <q-stepper-navigation>

                        <q-btn flat color="negative" label="Cancel" class="q-ml-sm" @click="$inertia.get( route('employees.all'))"   />
                        <q-btn flat color="primary" label="Back" @click="prevStep" class="q-ml-sm" />
                        <q-btn color="primary" label="Next" @click="nextStep" />



                    </q-stepper-navigation>
                </q-step>

                <!-- Step 4: Preview -->
                <q-step
                    name="4"
                    title="Preview Information"
                    icon="visibility"
                    :done="step > 4"
                >

                    <div class="rounded-borders overflow-hidden">
                        <q-img
                            :src="previewUrl || defaultUrl"
                            alt="Profile Photo"
                            style="width: 120px; height: 120px"
                            img-class="object-cover"
                        />
                    </div>

                    <div class="q-mb-md text-h6">Personal Info</div>
                    <div class="row q-col-gutter-sm q-mb-md">
                        <div class="col-12 col-sm-6"> <strong>Name:</strong> {{ form.name }} </div>
                        <div class="col-12 col-sm-6"> <strong>Email:</strong> {{ form.email }} </div>
                        <div class="col-12 col-sm-6"> <strong>Mobile:</strong> {{ form.mobile }} </div>
                        <div class="col-12 col-sm-6"> <strong>Parent Name:</strong> {{ form.parent_name }} </div>
                        <div class="col-12 col-sm-6"> <strong>Date of Birth:</strong> {{ formatDate(form.date_of_birth) }} </div>
                        <div class="col-12 col-sm-6"> <strong>Education:</strong> {{ form.educational_qln }} </div>
                        <div class="col-12 col-sm-6"> <strong>Technical Qualification:</strong> {{ form.technical_qln }} </div>
                    </div>

                    <div class="q-mb-md text-h6">Job Info</div>
                    <div class="row q-col-gutter-sm q-mb-md">
                        <div class="col-12 col-sm-6"> <strong>Employment Type:</strong> Muster Roll</div>
<!--                        <div class="col-12 col-sm-6"> <strong>Office:</strong> {{ offices[form.office].name }} </div>-->
                        <div class="col-12 col-sm-6">
                            <strong>Office:</strong>
                            {{ (props?.offices || offices).find(o => String(o.id) === String(form.office))?.name || '' }}
                        </div>
                        <div class="col-12 col-sm-6"> <strong>Post/Work Assigned:</strong> {{ form.post_assigned }} </div>
                        <div class="col-12 col-sm-6"> <strong>Date of Initial Engagement:</strong> {{ formatDate(form.date_of_engagement) }} </div>
                        <div class="col-12 col-sm-6"> <strong>Skill Category at Initial Engagement:</strong> {{ form.skill_category }} </div>
                        <div class="col-12 col-sm-6"> <strong>Skill at Present:</strong> {{ form.skill_at_present }} </div>
                        <div class="col-12 col-sm-6"> <strong>Post Selected:</strong> {{ form.post_per_qualification }} </div>
                        <div class="col-12 col-sm-6"> <strong>Workplace:</strong> {{ form.name_of_workplace }} </div>

                        <div v-if="isScheme" class="col-12 col-sm-6"><strong>Scheme:</strong>{{ (props?.schemes || schemes).find(o => String(o.id) === String(form.scheme))?.name || '' }}</div>


                    </div>

                    <div class="q-mb-md text-h6">Uploaded Documents</div>
                    <div class="row q-col-gutter-sm q-mb-md">
                        <div
                            class="col-12 col-sm-6"
                            v-for="(type, index) in documentTypes"
                            :key="type.id"
                        >
                            <div class="row items-center justify-between">
                                <div class="text-subtitle2"><strong>{{ type.name }}</strong></div>

                                <div v-if="form.documents[type.id]">
                                    <div class="row items-center no-wrap">

                                        <a
                                            :href="getFileUrl(form.documents[type.id])"
                                            target="_blank"
                                            class="text-primary text-weight-medium"
                                        >
                                            <q-icon name="visibility" class="q-mr-xs" />
                                        </a>
                                    </div>
                                </div>
                                <div v-else class="text-grey">No document uploaded</div>
                            </div>
                        </div>
                    </div>

                    <q-stepper-navigation>
                        <q-btn flat color="negative" label="Cancel" class="q-ml-sm" @click="$inertia.get(route('employees.all'))" />
                        <q-btn flat color="primary" label="Back" @click="prevStep" class="q-ml-sm" />
                        <q-btn type="submit" color="btn-primary" label="Submit" />
                    </q-stepper-navigation>
                </q-step>
            </q-stepper>
        </q-form>

    </q-page>
</template>
<script setup>
import BackendLayout from "@/Layouts/BackendLayout.vue";
import {useForm,router} from "@inertiajs/vue3";
import {useQuasar} from "quasar";
import {ref} from 'vue'

import useUtils from "@/Compositions/useUtils";

const {  educationalQualifications, skills, formatDate } = useUtils();

defineOptions({layout:BackendLayout})
const props=defineProps(['documentTypes','offices','schemes']);
const $q = useQuasar();


const isScheme = ref(false);


const step = ref('1')


const form=useForm({
    avatar:null,
    name:'',
    email:'',
    mobile:'',
    parent_name:'',
    date_of_birth:'',
    address:'',
    post_assigned:'',
    employment_type:'MR',
    office:'',
    educational_qln:'',
    technical_qln:'',
    name_of_workplace:'',
    post_per_qualification:'',
    date_of_engagement:'',
    skill_category:'',
    skill_at_present:'',
    scheme:'',
    documents:[],
})

// Default profile image
const defaultUrl = 'https://storage.googleapis.com/a1aa/image/089155c9-d1c1-4945-5728-c9bdb3576171.jpg'

const previewUrl = ref(null)
const fileInput = ref(null)

function triggerFileInput() {
    fileInput.value?.click()
}

function handleFileChange(event) {
    const file = event.target.files[0]
    if (file && file.size <= 800 * 1024) {
        const reader = new FileReader()
        reader.onload = () => {
            previewUrl.value = reader.result
        }
        form.avatar = file
        reader.readAsDataURL(file)
    } else {
        alert('File too large or invalid type. Max size: 800KB.')
    }
}

function resetPhoto() {
    previewUrl.value = null
    fileInput.value.value = null
    form.avatar = null
}

const nextStep = () => {
    if (step.value === '1') {
        if (
            !form.name || !form.mobile || !form.parent_name ||
            !form.date_of_birth || !form.educational_qln
        ) {
            $q.notify({ type: 'negative', message: 'Please fill all required Personal Info fields.' })
            return
        }
        step.value = '2'
    } else if (step.value === '2') {
        if (
            !form.employment_type ||!form.post_assigned || !form.office || !form.skill_category || !form.skill_at_present || !form.date_of_engagement

        ) {
            $q.notify({ type: 'negative', message: 'Please fill all required Job Info fields.' })
            return
        }

        step.value = '3'
    } else if (step.value === '3') {
        // Optional: check if at least one document is uploaded, or skip validation
        step.value = '4'
    }
}




const prevStep = () => {
    if (step.value === '4') step.value = '3'
    else if (step.value === '3') step.value = '2'
    else if (step.value === '2') step.value = '1'
}
const getFileUrl = (file) => {
    if (!file) return '#'
    return typeof file === 'string' ? file : URL.createObjectURL(file)
}


const type = [
    { label: 'MR', value: 'MR' },
    { label: 'PE', value: 'PE' },
]


const submit = () => {

    $q.dialog({
        title: 'Confirm Submission',
        message: 'Are you sure you want to submit?',
        cancel: true,
        persistent: true
    }).onOk(() => {
        const formData = new FormData()
        if (form.avatar) {
            formData.append('avatar', form.avatar)
        }
        formData.append('name', form.name)
        formData.append('email', form.email)
        formData.append('mobile', form.mobile)
        formData.append('parent_name', form.parent_name)
        formData.append('date_of_birth', form.date_of_birth)
        formData.append('address', form.address)
        formData.append('post_assigned', form.post_assigned)
        formData.append('employment_type', form.employment_type)
        formData.append('office', form.office)
        formData.append('educational_qln', form.educational_qln)
        formData.append('technical_qln', form.technical_qln)
        formData.append('name_of_workplace', form.name_of_workplace)
        formData.append('post_per_qualification', form.post_per_qualification)
        formData.append('date_of_engagement', form.date_of_engagement)
        formData.append('skill_category', form.skill_category)
        formData.append('skill_at_present', form.skill_at_present)
        formData.append('scheme', form.scheme)


        // Append documents (assuming object with id as key and File as value)
        for (const id in form.documents) {
            if (form.documents[id]) {
                formData.append(`documents[${id}]`, form.documents[id])
            }
        }

        axios.post(route('employee.store'), formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        })
            .then(response => {
                const {employee,message,} = response.data;
                $q.notify({
                    type: 'positive',
                    message: response.data.message || 'Employee saved successfully.'
                })
                form.reset()
                router.get(route('employee.show', employee))
            })
            .catch(error => {
                if (error.response) {
                    const errors = error.response.data.errors
                    const messages = []
                    let firstErrorField = null

                    if (errors) {
                        for (const field in errors) {
                            if (!firstErrorField) {
                                firstErrorField = field
                            }
                            if (errors[field]) {
                                messages.push(...errors[field])
                            }
                        }

                        // Set errors to your form object
                        form.errors = errors

                        // Move to the step that contains the first error
                        if (firstErrorField) {
                            const stepMap = {
                                // map fields to their corresponding step numbers
                                name: 1,
                                email: 1,
                                mobile: 1,
                                parent_name: 1,
                                date_of_birth: 1,
                                educational_qln: 1,
                                technical_qln: 1,

                                employment_type: 2,
                                designation: 2,
                                date_of_engagement: 2,
                                name_of_workplace: 2,
                                office: 2,
                                post_per_qualification: 2,
                                skill_category: 2,
                                skill_at_present: 2,
                                scheme:2,

                                documents: 3,
                            }

                            const errorStep = stepMap[firstErrorField] || 1
                            step.value = String(errorStep)  // ✅ convert to string to match your logic

                        }

                        $q.notify({
                            type: 'negative',
                            message: messages.join(', '),
                        })

                    } else if (error.response.data.message) {
                        $q.notify({
                            type: 'negative',
                            message: error.response.data.message,
                        })
                    }
                } else {
                    $q.notify({
                        type: 'negative',
                        message: 'System Error. Please try again later.'
                    })
                }
            })
    })
}

const goBack = () => {
    window.history.back()
}

</script>



