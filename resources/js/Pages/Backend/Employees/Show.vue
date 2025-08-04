<template>
    <q-page class="container" padding>
        <div class="row q-col-gutter-md">
            <!-- Left Panel -->
            <div class="col-12 col-md-5">
                <q-card class="q-pa-md">
                    <div class="column items-center q-gutter-sm">
                        <q-avatar size="96px">
                            <q-img :src="`/storage/${data.avatar}`" />
                        </q-avatar>

                        <div class="text-h6">{{ data.name }}</div>
                        <q-badge color="grey-4" text-color="dark" label="Admin" />



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
                                    <q-item-section side class="text-black">Office Type:</q-item-section>
                                    <q-item-section  class="text-grey">{{data?.office?.type}}</q-item-section>
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
            <div class="col-12 col-md-7">
                <!-- Tabs -->
                <q-tabs
                    v-model="tab"
                    class="text-primary bg-white shadow-1 q-pa-sm"
                    dense
                    align="center"
                >
                    <q-tab name="document" label="Documents" icon="drive_folder_upload" />
                    <q-tab name="edit" label="Edit Request" icon="edit"/>
                    <q-tab name="transfer" label="Transfer Request" icon="input" />
                    <q-tab name="deletion" label="Deletion Request" icon="delete_sweep" />
                </q-tabs>

                <q-card class="q-mt-md">
                    <q-card-section>
                        <div class="text-h6">Uploaded Documents</div>

                        <div class="row q-col-gutter-md q-mt-sm">
                            <q-input
                                v-model="search"
                                dense
                                outlined
                                label="Search by document name or type..."
                                class="col-12 col-sm-6"
                            />
                        </div>
                    </q-card-section>

                    <q-markup-table flat dense class="q-ma-sm">
                        <thead>
                        <tr class="text-left">
                            <th>Document Name</th>
                            <th>Type</th>
                            <th>Mime Type</th>
                            <th>Upload Date</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr
                            v-for="doc in filteredDocuments"
                            :key="doc.id"
                            class="text-left"
                        >
                            <td class="q-pa-sm">{{ doc.name }}</td>
                            <td class="q-pa-sm">{{ doc.type?.name || '—' }}</td>
                            <td class="q-pa-sm">{{ doc.mime }}</td>
                            <td class="q-pa-sm">{{ formatDate(doc.upload_date) }}</td>
                            <td class="q-pa-sm">
                                <q-btn
                                    icon="visibility"
                                    flat
                                    dense
                                    round
                                    :href="`/storage/${doc.path}`"
                                    target="_blank"
                                    title="View Document"
                                />
                                <q-btn
                                    icon="download"
                                    flat
                                    dense
                                    round
                                    :href="`/storage/${doc.path}`"
                                    download
                                    title="Download Document"
                                />
                            </td>
                        </tr>
                        <tr v-if="filteredDocuments.length === 0">
                            <td colspan="5" class="text-center text-grey">No documents found</td>
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


const filteredDocuments = computed(() => {
    if (!props.data?.documents) return [];

    return props.data.documents.filter((doc) => {
        const searchText = search.value.toLowerCase();
        return (
            doc.name?.toLowerCase().includes(searchText) ||
            doc.type?.name?.toLowerCase().includes(searchText)
        );
    });
});

function formatDate(date) {
    if (!date) return '';
    return new Date(date).toLocaleDateString();
}
</script>
