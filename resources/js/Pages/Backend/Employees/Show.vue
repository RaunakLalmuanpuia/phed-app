<template>
    <q-page class="container" padding>
        <div class="row q-col-gutter-md">
            <!-- Left Panel -->
            <div class="col-12 col-md-4">
                <q-card class="q-pa-md">
                    <div class="column items-center q-gutter-sm">
                        <q-avatar size="96px">
                            <img src="https://storage.googleapis.com/a1aa/image/ce4ce84e-7065-465f-056e-505939d6ea1d.jpg" />
                        </q-avatar>
                        <div class="text-h6">{{ data.name }}</div>
                        <q-badge color="grey-4" text-color="dark" label="Admin" />

                        <div class="row q-col-gutter-sm q-mt-md gap-5">
                            <q-card class="col bg-indigo-2 text-indigo-10 text-center q-pa-sm">
                                <q-icon name="check_square" size="20px" />
                                <div class="text-subtitle2">1,230</div>
                                <div class="text-caption">Task Done</div>
                            </q-card>
                            <q-card class="col bg-indigo-2 text-indigo-10 text-center q-pa-sm">
                                <q-icon name="work" size="20px" />
                                <div class="text-subtitle2">568</div>
                                <div class="text-caption">Project Done</div>
                            </q-card>
                        </div>

                        <div class="q-mt-lg full-width">
                            <div class="text-caption text-grey-7 q-mb-sm">Details</div>
                            <q-list bordered class="rounded-borders">
                                <q-item>
                                    <q-item-section side class="text-black">Employee Code:</q-item-section>
                                    <q-item-section  class="text-grey">{{ data?.employee_code }}</q-item-section>
                                </q-item>
                                <q-item>
                                    <q-item-section side class="text-black">Name:</q-item-section>
                                    <q-item-section  class="text-grey">{{ data?.name }}</q-item-section>
                                </q-item>
                                <q-item>
                                    <q-item-section side class="text-black">Email:</q-item-section>
                                    <q-item-section  class="text-grey">{{data.email}}</q-item-section>
                                </q-item>
                                <q-item>
                                    <q-item-section side class="text-black">Contact:</q-item-section>
                                    <q-item-section  class="text-grey">{{data?.mobile}}</q-item-section>
                                </q-item>
                                <q-item>
                                    <q-item-section side class="text-black">Parent Name:</q-item-section>
                                    <q-item-section  class="text-grey">{{data?.parent_name}}</q-item-section>
                                </q-item>

                                <q-item>
                                    <q-item-section side class="text-black">Date of Birth:</q-item-section>
                                    <q-item-section  class="text-grey">{{data.date_of_birth}}</q-item-section>
                                </q-item>

                                <q-item>
                                    <q-item-section side class="text-black">Education Qln.:</q-item-section>
                                    <q-item-section  class="text-grey">{{data?.educational_qln}}</q-item-section>
                                </q-item>

                                <q-item>
                                    <q-item-section side class="text-black">Technical Qln.:</q-item-section>
                                    <q-item-section  class="text-grey">{{data?.technical_qln}}</q-item-section>
                                </q-item>

                                <q-item>
                                    <q-item-section side class="text-black">Employment Type:</q-item-section>
                                    <q-item-section  class="text-grey">{{data?.employment_type}}</q-item-section>
                                </q-item>

                                <q-item>
                                    <q-item-section side class="text-black" >Designation:</q-item-section>
                                    <q-item-section  class="text-grey">{{data?.designation}}</q-item-section>
                                </q-item>

                                <q-item>
                                    <q-item-section side class="text-black">Office:</q-item-section>
                                    <q-item-section  class="text-grey">{{data?.office?.name}}</q-item-section>
                                </q-item>

                                <q-item>
                                    <q-item-section side class="text-black">Workplace:</q-item-section>
                                    <q-item-section  class="text-grey">{{data?.name_of_workplace}}</q-item-section>
                                </q-item>

                                <q-item>
                                    <q-item-section side class="text-black">Post Per Qualification:</q-item-section>
                                    <q-item-section  class="text-grey">{{data?.post_per_qualification}}</q-item-section>
                                </q-item>

                                <q-item>
                                    <q-item-section side class="text-black">Date of Engagement:</q-item-section>
                                    <q-item-section  class="text-grey">{{data?.date_of_engagement ?? 'N/A'}}</q-item-section>
                                </q-item>

                                <q-item>
                                    <q-item-section side class="text-black">Skill Category:</q-item-section>
                                    <q-item-section  class="text-grey">{{data?.skill_category}}</q-item-section>
                                </q-item>

                                <q-item>
                                    <q-item-section side class="text-black">Skill at Present:</q-item-section>
                                    <q-item-section class="text-grey">{{data?.skill_at_present}}</q-item-section>
                                </q-item>
                            </q-list>
                        </div>
                    </div>
                </q-card>
            </div>

            <!-- Right Panel -->
            <div class="col-12 col-md-8">
                <!-- Tabs -->
                <q-tabs
                    v-model="tab"
                    class="text-primary bg-white shadow-1 q-pa-sm"
                    dense
                    align="left"
                >
                    <q-tab name="account" label="Account" icon="account_circle" />
                    <q-tab name="security" label="Security" icon="lock" />
                    <q-tab name="billing" label="Billing & Plan" icon="attach_money" />
                    <q-tab name="notification" label="Notification" icon="notifications" />
                    <q-tab name="connections" label="Connections" icon="link" />
                </q-tabs>

                <!-- Projects Table -->
                <q-card class="q-mt-md">
                    <q-card-section>
                        <div class="text-h6">User's Projects List</div>
                        <div class="row q-col-gutter-md q-mt-sm">
                            <q-select
                                dense
                                outlined
                                v-model="rowsPerPage"
                                :options="[5, 10, 15]"
                                label="Rows per page"
                                class="col-12 col-sm-3"
                            />
                            <q-input
                                v-model="search"
                                dense
                                outlined
                                label="Search..."
                                class="col-12 col-sm-6"
                            />
                        </div>
                    </q-card-section>

                    <q-markup-table flat dense class="q-ma-sm">
                        <thead>
                        <tr>
                            <th>Project</th>
                            <th>Total Tasks</th>
                            <th>Progress</th>
                            <th>Hours</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="project in filteredProjects" :key="project.name">
                            <td class="q-pa-sm">
                                <div class="row items-center q-gutter-sm">
                                    <q-avatar :style="`background-color: ${project.bg}`">
                                        <img :src="project.icon" />
                                    </q-avatar>
                                    <div>
                                        <div class="text-subtitle2">{{ project.name }}</div>
                                        <div class="text-caption text-grey">{{ project.type }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ project.tasks }}</td>
                            <td class="q-pa-sm">
                                <div class="row items-center q-gutter-sm">
                                    <span>{{ project.progress }}%</span>
                                    <q-linear-progress
                                        :value="project.progress / 100"
                                        color="primary"
                                        size="8px"
                                        rounded
                                        style="flex: 1"
                                    />
                                </div>
                            </td>
                            <td>{{ project.hours }}</td>
                        </tr>
                        </tbody>
                    </q-markup-table>
                </q-card>
            </div>

        </div>
    </q-page>
</template>

<script setup>
import BackendLayout from "@/Layouts/BackendLayout.vue";
import { ref, computed } from 'vue'

defineOptions({layout:BackendLayout})

const props=defineProps(['data']);

const tab = ref('account')
const rowsPerPage = ref(10)
const search = ref('')

const projects = ref([
    {
        name: 'BGC ECommerce App',
        type: 'React Project',
        tasks: '122/240',
        progress: 78,
        hours: '18:42',
        bg: '#dff6fc',
        icon: 'https://storage.googleapis.com/a1aa/image/cdc5ed3a-4964-4e37-57e3-d0531289231c.jpg'
    },
    {
        name: 'Falcon Logo Design',
        type: 'Figma Project',
        tasks: '09/56',
        progress: 18,
        hours: '20:42',
        bg: '#ffe3e0',
        icon: 'https://storage.googleapis.com/a1aa/image/b38b03f3-4d9f-42f3-bb39-91756b138acc.jpg'
    },
    {
        name: 'Dashboard Design',
        type: 'Vuejs Project',
        tasks: '290/320',
        progress: 62,
        hours: '120:87',
        bg: '#d6f9e6',
        icon: 'https://storage.googleapis.com/a1aa/image/d810dc81-ecda-48f5-7ceb-25d71926e515.jpg'
    },
    {
        name: 'Foodista Mobile App',
        type: 'Xamarin Project',
        tasks: '290/320',
        progress: 8,
        hours: '120:87',
        bg: '#1e90ff',
        icon: 'https://storage.googleapis.com/a1aa/image/d1d3866c-8c92-4472-6939-8053f6696b50.jpg'
    },
    {
        name: 'Dojo Email App',
        type: 'Python Project',
        tasks: '120/186',
        progress: 49,
        hours: '230:10',
        bg: '#2a6f9e',
        icon: 'https://storage.googleapis.com/a1aa/image/9150d703-bada-444f-9503-3eaf593ab2ac.jpg'
    },
    {
        name: 'Blockchain Website',
        type: 'Sketch Project',
        tasks: '99/109',
        progress: 92,
        hours: '342:12',
        bg: '#fff3b0',
        icon: 'https://storage.googleapis.com/a1aa/image/84fa9edd-2e26-4e8b-096c-204f22063d19.jpg'
    }
])

const filteredProjects = computed(() => {
    if (!search.value) return projects.value
    return projects.value.filter(p =>
        p.name.toLowerCase().includes(search.value.toLowerCase())
    )
})
</script>
