<template>
    <q-page class="container" padding>
        <div class="flex items-center justify-between q-pa-md bg-white">
            <div>
                <div class="stitle">Edit Employee</div>
                <q-breadcrumbs class="text-dark">
                    <q-breadcrumbs-el class="cursor-pointer"  icon="dashboard" label="Dashboard" @click="$inertia.get(route('dashboard'))"/>
                    <q-breadcrumbs-el class="cursor-pointer" label="All Employees"  @click="$inertia.get(isManagerOrViewer ? route('employees.manager.all') : route('employees.all'))"/>
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
                                    :src="previewUrl"
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


                    <div v-if="isManagerOrViewer" class="row q-col-gutter-sm">
                        <div class="col-12 col-sm-6">
                            <q-input v-model="form.name" label="Name" disable outlined dense :error="!!form.errors?.name"
                                     :error-message="form.errors?.name" :rules="[val => !!val || 'Name is required']" />
                        </div>
                        <div class="col-12 col-sm-6">
                            <q-input v-model="form.email" label="Email" type="email" outlined dense
                                     :error="!!form.errors?.email" :error-message="form.errors?.email" />
                        </div>

                        <div class="col-12 col-sm-6">
                            <q-input v-model="form.mobile" label="Mobile" mask="##########" outlined dense
                                     :error="!!form.errors?.mobile" :error-message="form.errors?.mobile"
                                     :rules="[val => !!val || 'Mobile is required']" />
                        </div>

                        <div class="col-12 col-sm-6">
                            <q-input v-model="form.address" label="Address" outlined dense
                                     :error="!!form.errors?.address" :error-message="form.errors?.address"/>
                        </div>

                        <div class="col-12 col-sm-6">
                            <q-input v-model="form.name_of_workplace" label="Workplace" outlined dense
                                     :error="!!form.errors?.name_of_workplace" :error-message="form.errors?.name_of_workplace"/>
                        </div>

                        <div class="col-12 col-sm-6">
                            <q-input v-model="form.technical_qln" label="Technical Qualification" outlined dense
                                     :error="!!form.errors?.technical_qln" :error-message="form.errors?.technical_qln"/>
                        </div>
                        <div v-if="form.employment_type === 'MR'" class="col-12 col-sm-6">
                            <q-input v-model="form.post_assigned" label="Post/Work Assigned" outlined dense
                                     :error="!!form.errors?.post_assigned" :error-message="form.errors?.post_assigned" />
                        </div>
                    </div>
                    <div v-if="!isManagerOrViewer " class="row q-col-gutter-sm">
                        <div class="col-12 col-sm-6">
                            <q-input v-model="form.name" label="Name" outlined dense :error="!!form.errors?.name"
                                     :error-message="form.errors?.name" :rules="[val => !!val || 'Name is required']" />
                        </div>
                        <div class="col-12 col-sm-6">
                            <q-input v-model="form.email" label="Email" type="email" outlined dense
                                     :error="!!form.errors?.email" :error-message="form.errors?.email" />
                        </div>
                        <div class="col-12 col-sm-6">
                            <q-input v-model="form.mobile" label="Mobile" mask="##########" outlined dense
                                     :error="!!form.errors?.mobile" :error-message="form.errors?.mobile"
                                     :rules="[val => !!val || 'Mobile is required']" />
                        </div>
                        <div class="col-12 col-sm-6">
                            <q-input v-model="form.parent_name" label="Parent Name" outlined dense
                                     :error="!!form.errors?.parent_name" :error-message="form.errors?.parent_name"
                                     :rules="[val => !!val || 'Parent Name is required']" />
                        </div>
                        <div class="col-12 col-sm-6">
                            <q-input v-model="form.date_of_birth" label="Date of Birth" type="date" outlined dense
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
                                     :error="!!form.errors?.technical_qln" :error-message="form.errors?.technical_qln"/>
                        </div>
                    </div>
                    <q-stepper-navigation>
                        <q-btn flat color="negative" label="Cancel" class="q-ml-sm" @click="goBack"  />
                        <q-btn color="primary" label="Next" @click="nextStep" />
                    </q-stepper-navigation>
                </q-step>

                <!-- Step 2: Job Info -->
                <q-step v-if="!isManagerOrViewer" name="2" title="Job Info" icon="work" :done="step > 2">
                    <div class="row q-col-gutter-sm">
                        <div class="col-12 col-sm-6">
                            <q-select v-model="form.employment_type" label="Employment Type *" :options="type"
                                      emit-value map-options outlined dense
                                      :error="!!form.errors?.employment_type" :error-message="form.errors?.employment_type"
                                      :rules="[val => !!val || 'Employment Type is required']" />
                        </div>
                        <div v-if="form.employment_type === 'PE' || form.employment_type === 'WC'" class="col-12 col-sm-6">
                            <q-input v-model="form.designation" label="Designation *" outlined dense
                                     :error="!!form.errors?.designation" :error-message="form.errors?.designation"
                                     :rules="[val => !!val || 'Designation is required']" />

                        </div>
                        <div v-if="form.employment_type === 'MR'" class="col-12 col-sm-6">
                            <q-input v-model="form.post_assigned" label="Post/Work Assigned *" type="text" outlined dense
                                     :error="!!form.errors?.post_assigned" :error-message="form.errors?.post_assigned"
                                     :rules="[val => !!val || 'Post/Work Assigned is required']"
                            />
                        </div>

                        <div class="col-12 col-sm-6">
                            <q-select v-model="form.office" label="Office *" :options="offices" option-label="name" option-value="id"
                                      emit-value map-options outlined dense
                                      :error="!!form.errors?.office" :error-message="form.errors?.office"
                                      :rules="[val => !!val || 'Office is required']" />
                        </div>


                        <div v-if="form.employment_type === 'PE'" class="col-12 col-sm-6">
                            <q-input v-model="form.engagement_card_no" label="Engagement Card No." type="text" outlined dense
                                     :error="!!form.errors?.engagement_card_no" :error-message="form.errors?.engagement_card_no"
                                     :rules="[val => !!val || 'Engagement Card No. is required']"
                            />
                        </div>

                        <div v-if="form.employment_type === 'PE' || form.employment_type === 'WC'" class="col-12 col-sm-6">
                            <q-input v-model="form.date_of_engagement" label="Date of Initial Engagement" type="date" outlined dense
                                     :error="!!form.errors?.date_of_engagement" :error-message="form.errors?.date_of_engagement"
                                     :rules="[val => !!val || 'Date of Initial Engagement is required']"
                            />
                        </div>

                        <div v-if="form.employment_type === 'WC'" class="col-12 col-sm-6">
                            <q-input v-model="form.date_of_retirement" label="Date of Retirement" type="date" outlined dense
                                     :error="!!form.errors?.date_of_retirement" :error-message="form.errors?.date_of_retirement"
                                     :rules="[val => !!val || 'Date of Retirement']"
                            />
                        </div>


                        <div v-if="form.employment_type === 'MR'" class="col-12 col-sm-6">
                            <q-input v-model="form.date_of_engagement" label="Date of Initial Engagement *" type="date" outlined dense
                                     :error="!!form.errors?.date_of_engagement" :error-message="form.errors?.date_of_engagement"
                                     :rules="[val => !!val || 'Date of Initial Engagement is required']"
                            />
                        </div>

                        <div v-if="form.employment_type === 'MR'" class="col-12 col-sm-6">
                            <q-select v-model="form.skill_category" label="Skill Category at Initial Engagement *" :options="skills"
                                      emit-value map-options outlined dense
                                      :error="!!form.errors?.skill_category" :error-message="form.errors?.skill_category"
                                      :rules="[val => !!val || 'Skill Category is required']" />
                        </div>

                        <div v-if="form.employment_type === 'MR'" class="col-12 col-sm-6">
                            <q-select v-model="form.skill_at_present" label="Skill at Present *" :options="skills"
                                      emit-value map-options outlined dense
                                      :error="!!form.errors?.skill_at_present" :error-message="form.errors?.skill_at_present"
                                      :rules="[val => !!val || 'Skill at Present is required']" />
                        </div>


                        <div class="col-12 col-sm-6">
                            <q-input v-model="form.name_of_workplace" label="Workplace" outlined dense
                                     :error="!!form.errors?.name_of_workplace" :error-message="form.errors?.name_of_workplace"
                            />
                        </div>

                        <div v-if="form.employment_type === 'MR'" class="col-12 col-sm-6">
                            <q-input v-model="form.post_per_qualification" label="Post Selected" outlined dense
                                     :error="!!form.errors?.post_per_qualification" :error-message="form.errors?.post_per_qualification"
                                     :rules="[val => !!val || 'Post per Qualification is required']" />
                        </div>

                        <div class="col-12 col-sm-6" v-if="form.employment_type === 'MR'">
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
                            <div class="col-12 col-sm-6">
                                <q-select v-model="form.scheme" label="Scheme *" :options="schemes" option-label="name" option-value="id"
                                          emit-value map-options outlined dense
                                          :error="!!form.errors?.scheme" :error-message="form.errors?.scheme"
                                          :rules="[val => !!val || 'Scheme is required']" />
                            </div>

                        </div>



                    </div>
                    <q-stepper-navigation>
                        <q-btn flat color="negative" label="Cancel" class="q-ml-sm" @click="goBack"  />
                        <q-btn flat color="primary" label="Back" @click="prevStep" class="q-ml-sm" />
                        <q-btn color="primary" label="Next" @click="nextStep" />
                    </q-stepper-navigation>
                </q-step>

                <!--                Step 3: Document upload-->
                <q-step v-if="!isManagerOrViewer" name="3" title="Upload Documents" icon="cloud_upload" :done="step > 3">
                    <div class="row q-col-gutter-sm">
                        <div
                            class="col-12 col-sm-6"
                            v-for="(type, index) in documentTypes"
                            :key="type.id"
                        >
                            <div class="text-subtitle2 q-mb-xs">{{ type.name }}</div>
                            <q-file
                                filled
                                :model-value="form.documents[type.id]?.file || null"
                                @update:model-value="val => updateDocument(type.id, val)"
                                label="Upload File"
                                accept=".pdf,.jpg,.jpeg,.png"
                                clearable
                                class="full-width"
                            >
                                <template v-if="form.documents[type.id]?.url" v-slot:append>
                                    <q-btn
                                        round
                                        dense
                                        flat
                                        icon="visibility"
                                        @click="viewDocument(form.documents[type.id].url)"
                                        class="q-ml-sm"
                                    />
                                </template>
                            </q-file>


                        </div>
                    </div>

                    <q-stepper-navigation>
                        <q-btn flat color="negative" label="Cancel" class="q-ml-sm" @click="goBack"  />
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

                    <div class="rounded-borders">
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
                        <div class="col-12 col-sm-6"> <strong>Present Address:</strong> {{ form.address }} </div>
                        <div class="col-12 col-sm-6"> <strong>Education:</strong> {{ form.educational_qln }} </div>
                        <div class="col-12 col-sm-6"> <strong>Technical Qualification:</strong> {{ form.technical_qln }} </div>
                    </div>



                    <div class="q-mb-md text-h6">Job Info</div>
                    <div class="row q-col-gutter-sm q-mb-md">
                        <div class="col-12 col-sm-6"> <strong>Employment Type:</strong> {{ form.employment_type }} </div>
                        <div class="col-12 col-sm-6"> <strong>Office:</strong>  {{ offices.find(o => o.id === form.office)?.name || '—' }} </div>
                        <div v-if="form.employment_type === 'PE' || form.employment_type ==='WC'" class="col-12 col-sm-6"> <strong>Designation:</strong> {{ form.designation }} </div>
                        <div v-if="form.employment_type === 'MR'" class="col-12 col-sm-6"> <strong>Post/Work Assigned :</strong> {{ form.post_assigned }} </div>
                        <div v-if="form.employment_type === 'PE'" class="col-12 col-sm-6"> <strong>Engagement Card No. :</strong> {{ form.engagement_card_no }} </div>
                        <div class="col-12 col-sm-6"> <strong>Date of Initial Engagement:</strong> {{ formatDate(form.date_of_engagement) }} </div>
                        <div  v-if="form.employment_type === 'WC'"  class="col-12 col-sm-6"> <strong>Date of Retirement:</strong> {{ formatDate(form.date_of_retirement) }} </div>

                        <div v-if="form.employment_type === 'MR'" class="col-12 col-sm-6"> <strong>Skill Category at Initial Engagement:</strong> {{ form.skill_category }} </div>
                        <div v-if="form.employment_type === 'MR'" class="col-12 col-sm-6"> <strong>Skill at Present:</strong> {{ form.skill_at_present }} </div>
                        <div v-if="form.employment_type === 'MR'" class="col-12 col-sm-6"> <strong>Post Selected:</strong> {{ form.post_per_qualification }} </div>
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

                                <div v-if="form.documents[type.id]?.file || form.documents[type.id]?.url">
                                    <a
                                        :href="getFileUrl(form.documents[type.id]?.file || form.documents[type.id]?.url)"
                                        target="_blank"
                                        class="text-primary text-weight-medium"
                                    >
                                        <q-icon name="visibility" class="q-mr-xs" />
                                        View File
                                    </a>
                                </div>
                                <div v-else class="text-grey">No document uploaded</div>

                            </div>
                        </div>
                    </div>

                    <q-stepper-navigation>
                        <q-btn flat color="negative" label="Cancel" class="q-ml-sm" @click="goBack"  />
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
import {useForm, router, usePage} from "@inertiajs/vue3";
import {useQuasar} from "quasar";
import {ref, onMounted, computed} from 'vue'


import useUtils from "@/Compositions/useUtils";

const {  educationalQualifications, skills,formatDate } = useUtils();
defineOptions({layout:BackendLayout})
const props=defineProps(['documentTypes','offices','data','current_offices','schemes']);
const $q = useQuasar();



const step = ref('1')

const isScheme = ref(!!props.data?.scheme_id || !!props.data?.scheme)

const form=useForm({
    name:props.data?.name,
    email:props.data?.email,
    mobile:props.data?.mobile,
    parent_name:props.data?.parent_name,
    date_of_birth:props.data?.date_of_birth,
    address:props.data?.address,
    designation:props.data?.designation,
    post_assigned:props.data?.post_assigned,
    employment_type:props.data?.employment_type,
    office: props.data?.office?.id ?? null, // pick the ID only
    educational_qln:props.data?.educational_qln,
    technical_qln:props.data?.technical_qln,
    name_of_workplace:props.data?.name_of_workplace,
    post_per_qualification:props.data?.post_per_qualification,
    engagement_card_no: props.data?.engagement_card_no,
    date_of_engagement:props.data?.date_of_engagement,
    date_of_retirement:props.data?.date_of_retirement,
    skill_category:props.data?.skill_category,
    skill_at_present:props.data?.skill_at_present,
    scheme: props.data?.scheme?.id ?? null,
    documents:[],
    avatar:null,
    delete_avatar: false,
})
const fileInput = ref(null)
const defaultUrl = 'https://storage.googleapis.com/a1aa/image/089155c9-d1c1-4945-5728-c9bdb3576171.jpg'



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
    previewUrl.value =  defaultUrl
    form.avatar = null
    fileInput.value.value = null
    form.delete_avatar = true

}

// ✅ Use reactive URL that changes on edit or file upload
const previewUrl = ref('')
const updateDocument = (typeId, file) => {
    form.documents[typeId] = {
        ...form.documents[typeId],
        file // replace or add new
    }
}

const viewDocument = (url) => {
    window.open(url, '_blank')
}

const nextStep = () => {
    if (step.value === '1') {
        if (
            !form.name || !form.mobile ||
            !form.date_of_birth
        ) {
            $q.notify({ type: 'negative', message: 'Please fill all required Personal Info fields.' })
            return
        }
        // If admin → go to Step 2, else skip directly to Step 3
        step.value = !isManagerOrViewer.value ? '2' : '4'
    } else if (step.value === '2') {
        // Base required fields
        if (!form.employment_type || !form.office) {
            $q.notify({ type: 'negative', message: 'Please fill all required Job Info fields.' })
            return
        }

        // Conditional validation based on employment_type
        if (form.employment_type === 'MR') {
            // Example: for PE, require post_per_qualification and technical_qln
            if (!form.skill_category || !form.skill_at_present || !form.date_of_engagement) {
                $q.notify({ type: 'negative', message: 'Please fill all required Job Info fields.' })
                return
            }
        }

        step.value = '3'
    } else if (step.value === '3') {
        // Optional: check if at least one document is uploaded, or skip validation
        step.value = '4'
    }
}

const prevStep = () => {
    if (step.value === '4') {
        // If admin → back to Step 3, else back to Step 1
        step.value = !isManagerOrViewer.value ? '3' : '1'
    } else if (step.value === '3') {
        // If admin → back to Step 2, else back to Step 1
        step.value = !isManagerOrViewer.value ? '2' : '1'
    } else if (step.value === '2') {
        step.value = '1'
    }
}

const getFileUrl = (file) => {
    if (!file) return '#'
    return typeof file === 'string' ? file : URL.createObjectURL(file)
}


const type = [
    { label: 'MR', value: 'MR' },
    { label: 'PE', value: 'PE' },
    { label: 'WC', value: 'WC' },
]


const submit = () => {

    $q.dialog({
        title: 'Confirm Submission',
        message: 'Are you sure you want to submit?',
        cancel: true,
        persistent: true
    }).onOk(() => {
        const formData = new FormData()
        formData.append('name', form.name || '')
        formData.append('email', form.email || '')
        formData.append('mobile', form.mobile || '')
        formData.append('parent_name', form.parent_name || '')
        formData.append('date_of_birth', form.date_of_birth || '')
        formData.append('address', form.address || '')
        formData.append('designation', form.designation || '')
        formData.append('post_assigned', form.post_assigned || '')
        formData.append('employment_type', form.employment_type || '')
        formData.append('office', form.office?.id || form.office || '')
        formData.append('educational_qln', form.educational_qln || '')
        formData.append('technical_qln', form.technical_qln || '')
        formData.append('name_of_workplace', form.name_of_workplace || '')
        formData.append('post_per_qualification', form.post_per_qualification || '')
        formData.append('engagement_card_no', form.engagement_card_no || '')
        formData.append('date_of_engagement', form.date_of_engagement || '')
        formData.append('date_of_retirement', form.date_of_retirement || '')
        formData.append('skill_category', form.skill_category || '')
        formData.append('skill_at_present', form.skill_at_present || '')

        if (!isScheme.value) {
            form.scheme = ''
        }
        formData.append('scheme', form.scheme?.id || form.scheme)

        if (form.delete_avatar) {
            formData.append('delete_avatar', 1)
        }

        if (form.avatar && typeof form.avatar !== 'string') {
            formData.append('avatar', form.avatar)
        }

        // Append documents (assuming object with id as key and File as value)
        Object.entries(form.documents).forEach(([typeId, doc]) => {
            if (doc?.file) {
                formData.append(`documents[${typeId}]`, doc.file)
            }
        })

        axios.post(route('employee.update', props.data), formData, {
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
                                date_of_retirement: 2,
                                name_of_workplace: 2,
                                office: 2,
                                post_per_qualification: 2,
                                skill_category: 2,
                                skill_at_present: 2,

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
onMounted(() => {
    if (props.data.documents?.length) {
        props.data.documents.forEach(doc => {
            // Use document_type_id as key, store full path or file object
            form.documents[doc.document_type_id] = {
                name: doc.name,
                url: `/storage/${doc.path}`
            }
        })
    }

    if (props.data?.avatar) {
        previewUrl.value = `/storage/${props.data.avatar}` // Laravel public storage path
    } else {
        previewUrl.value = defaultUrl
    }
})


const isAdmin = computed(() => !!usePage().props.roles?.find(item => item === 'Admin'));
const isManager = computed(() => !!usePage().props.roles?.find(item => item === 'Manager'));

const isViewer = computed(() => !!usePage().props.roles?.find(item => item === 'Viewer'));


// Optional: You might want a combined computed property for convenience
const isManagerOrViewer = computed(() => isManager.value || isViewer.value);
const goBack = () => {
    window.history.back()
}

</script>
