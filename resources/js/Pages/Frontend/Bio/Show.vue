<template>
    <q-page class="container q-pa-md" padding>

        <div class="flex items-center justify-between q-pa-md bg-white">
            <div>
                <div class="stitle">Employee Bio</div>
                <q-breadcrumbs class="text-dark">
                    <q-breadcrumbs-el class="cursor-pointer" @click="$inertia.get(route('home'))" icon="dashboard" label="Home"/>
                    <q-breadcrumbs-el class="cursor-pointer" label="Go Back" @click="handleBack"/>
                </q-breadcrumbs>
            </div>
        </div>

        <br/>

        <q-card flat >
            <q-card-section>
                <div class="row q-col-gutter-md">
                    <!-- Left Panel -->
                    <div class="col-12 col-md-4">
                        <q-card class="q-pa-md">
                            <div class="column items-center q-gutter-sm">


                                <q-avatar size="96px">
                                    <q-img
                                        v-if="data?.avatar"
                                        :src="`/storage/${data?.avatar}`"
                                    />
                                    <q-icon v-else name="person"  color="primary" />
                                </q-avatar>

                                <div class="stitle">{{ data.name }}</div>
                                <q-badge
                                    :color="data?.employment_type?.trim() === 'Deleted' ? 'red-6' : 'grey-6'"
                                    text-color="white"
                                    class="stitle"
                                    :label="data?.employment_type?.trim() === 'MR'
                                        ? 'Muster Roll Employee'
                                        : data?.employment_type?.trim() === 'PE'
                                          ? 'Provisional Employee'
                                          : 'Deleted Employee'"
                                    style="padding: 8px;"
                                    rounded
                                />
                                <div class="q-mt-lg full-width">

                                    <q-list  class="rounded-borders">
                                        <q-item>
                                            <q-item-section  class="text-label">Details</q-item-section>
                                        </q-item>
                                        <q-item>
                                            <q-item-section side class="subtitle">Employee Code:</q-item-section>
                                            <q-item-section  class="text-label">{{ data?.employee_code }}</q-item-section>
                                        </q-item>
                                        <q-item>
                                            <q-item-section side class="subtitle">Name:</q-item-section>
                                            <q-item-section  class="text-label">{{ data?.name }}</q-item-section>
                                        </q-item>
                                        <q-item>
                                            <q-item-section side class="subtitle">Email:</q-item-section>
                                            <q-item-section  class="text-label">{{data.email}}</q-item-section>
                                        </q-item>
                                        <q-item>
                                            <q-item-section side class="subtitle">Contact:</q-item-section>
                                            <q-item-section  class="text-label">{{data?.mobile}}</q-item-section>
                                        </q-item>
                                        <q-item>
                                            <q-item-section side class="subtitle">Parent Name:</q-item-section>
                                            <q-item-section  class="text-label">{{data?.parent_name}}</q-item-section>
                                        </q-item>

                                        <q-item>
                                            <q-item-section side class="subtitle">Date of Birth:</q-item-section>
                                            <q-item-section  class="text-label">{{formatDate(data.date_of_birth)}}</q-item-section>
                                        </q-item>

                                        <q-item>
                                            <q-item-section side class="subtitle">Address:</q-item-section>
                                            <q-item-section  class="text-label">{{data?.address ?? 'N/A'}}</q-item-section>
                                        </q-item>

                                        <q-item>
                                            <q-item-section side class="subtitle">Education Qln.:</q-item-section>
                                            <q-item-section  class="text-label">{{data?.educational_qln}}</q-item-section>
                                        </q-item>

                                        <q-item>
                                            <q-item-section side class="subtitle">Technical Qln.:</q-item-section>
                                            <q-item-section  class="text-label">{{data?.technical_qln}}</q-item-section>
                                        </q-item>

                                        <q-item v-if=" data?.employment_type?.trim() === 'PE' || (data?.designation && data?.designation !== 'null')">
                                            <q-item-section side class="subtitle">Designation:</q-item-section>
                                            <q-item-section  class="text-label">{{data?.designation}}</q-item-section>
                                        </q-item>

                                        <q-item v-if=" data?.employment_type?.trim() === 'MR' || (data?.post_assigned && data?.post_assigned !== 'null')">
                                            <q-item-section side class="subtitle">Post/Work Assigned:</q-item-section>
                                            <q-item-section  class="text-label">{{data?.post_assigned}}</q-item-section>
                                        </q-item>

                                        <q-item>
                                            <q-item-section side class="subtitle">Office:</q-item-section>
                                            <q-item-section  class="text-label">{{data?.office?.name}}</q-item-section>
                                        </q-item>

                                        <q-item>
                                            <q-item-section side class="subtitle">Office Type:</q-item-section>
                                            <q-item-section  class="text-label">{{data?.office?.type}}</q-item-section>
                                        </q-item>

                                        <q-item>
                                            <q-item-section side class="subtitle">Workplace:</q-item-section>
                                            <q-item-section  class="text-label">{{data?.name_of_workplace}}</q-item-section>
                                        </q-item>

                                        <q-item>
                                            <q-item-section side class="subtitle">Date of Initial Engagement:</q-item-section>
                                            <q-item-section class="text-label">
                                                {{ formatDate(data?.date_of_engagement) || 'N/A' }}
                                            </q-item-section>
                                        </q-item>

                                        <q-item v-if=" data?.employment_type?.trim() === 'PE' || (data?.designation && data?.designation !== 'null')">
                                            <q-item-section side class="subtitle">Engagement Card No. :</q-item-section>
                                            <q-item-section  class="text-label">{{data?.engagement_card_no}}</q-item-section>
                                        </q-item>

                                        <q-item v-if=" data?.employment_type?.trim() === 'MR' || (data?.post_assigned && data?.post_assigned !== 'null')">
                                            <q-item-section side class="subtitle">Post Selected:</q-item-section>
                                            <q-item-section  class="text-label">{{data?.post_per_qualification}}</q-item-section>
                                        </q-item>

                                        <q-item v-if=" data?.employment_type?.trim() === 'MR' || (data?.post_assigned && data?.post_assigned !== 'null')">
                                            <q-item-section side class="subtitle">Skill Category:</q-item-section>
                                            <q-item-section  class="text-label">{{data?.skill_category}}</q-item-section>
                                        </q-item>

                                        <q-item v-if=" data?.employment_type?.trim() === 'MR' || (data?.post_assigned && data?.post_assigned !== 'null')">
                                            <q-item-section side class="subtitle">Skill at Present:</q-item-section>
                                            <q-item-section class="text-label">{{data?.skill_at_present}}</q-item-section>
                                        </q-item>


                                        <q-item v-if=" data?.employment_type?.trim() === 'MR' && data?.scheme_id || (data?.post_assigned && data?.post_assigned !== 'null')">
                                            <q-item-section side class="subtitle">Scheme:</q-item-section>
                                            <q-item-section class="text-label">{{data?.scheme?.name}}</q-item-section>
                                        </q-item>
                                    </q-list>


                                </div>
                            </div>
                        </q-card>
                    </div>

                    <!-- Right Panel -->
                    <div class="col-12 col-md-8">

                        <q-card class="q-pa-md">
                            <q-card-section class="flex items-center justify-between">
                                <div class="stitle text-lg font-bold">Uploaded Documents</div>
                            </q-card-section>

                            <q-card-section>
                                <div v-if="filteredDocuments.length === 0" class="text-grey-7 text-center q-pa-md italic">
                                    No documents uploaded
                                </div>

                                <div v-if="filteredDocuments.length > 0">

                                    <div class="row q-col-gutter-md q-mt-sm">
                                        <q-input
                                            v-model="search"
                                            dense
                                            outlined
                                            label="Search by document type..."
                                            class="col-12 col-sm-6"
                                        />
                                    </div>

                                    <q-markup-table flat dense class="q-ma-sm">
                                        <thead>
                                        <tr class="text-left">
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
                                            <td class="q-pa-sm">{{ doc.type?.name || '—' }}</td>
                                            <td class="q-pa-sm">{{ doc.mime }}</td>
                                            <td class="q-pa-sm">{{ formatDate(doc.upload_date) }}</td>
                                            <td class="q-pa-sm">
                                                <q-btn
                                                    icon="visibility"
                                                    flat
                                                    dense
                                                    color="primary"
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
                                                    color="primary"
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
                                </div>
                            </q-card-section>

                        </q-card>


                        <div class="row q-col-gutter-md">
                            <div class="col-12 col-md-6">
<!--                                Remuneration-->
                                <q-card class="q-mt-md">
                                    <div class="flex items-center justify-between q-pa-md bg-white">
                                        <div>
                                            <div class="stitle">Remuneration Detail</div>
                                        </div>
                                    </div>

                                    <q-card-section>
                                        <div v-if="!data.remuneration_detail" class="text-grey-7 text-center q-pa-md italic">
                                            No remuneration data available.
                                        </div>

                                        <q-list v-else class="q-mt-sm">
                                            <q-item>
                                                <q-item-section side class="subtitle">Remuneration:</q-item-section>
                                                <q-item-section class="text-label">
                                                    ₹ {{ data.remuneration_detail.remuneration.toLocaleString() }}
                                                </q-item-section>
                                            </q-item>

                                            <q-item>
                                                <q-item-section side class="subtitle">Pay Matrix:</q-item-section>
                                                <q-item-section class="text-label">
                                                    {{ data.remuneration_detail.pay_matrix ?? 'N/A' }}
                                                </q-item-section>
                                            </q-item>

                                            <q-item>
                                                <q-item-section side class="subtitle">Medical Allowance (%):</q-item-section>
                                                <q-item-section class="text-label">
                                                    {{ data.remuneration_detail.medical_percentage }}
                                                </q-item-section>
                                            </q-item>

                                            <q-item>
                                                <q-item-section side class="subtitle">Medical Allowance:</q-item-section>
                                                <q-item-section class="text-label">
                                                    ₹ {{ data.remuneration_detail.medical_amount.toLocaleString() }}
                                                </q-item-section>
                                            </q-item>

                                            <q-item>
                                                <q-item-section side class="subtitle">Total:</q-item-section>
                                                <q-item-section class="text-label">
                                                    ₹ {{ data.remuneration_detail.total.toLocaleString() }}
                                                </q-item-section>
                                            </q-item>

                                            <q-item>
                                                <q-item-section side class="subtitle">Monthly Remuneration:</q-item-section>
                                                <q-item-section class="text-label">
                                                    ₹ {{ data.remuneration_detail.round_total.toLocaleString() }}
                                                </q-item-section>
                                            </q-item>

                                            <q-separator spaced />

                                            <q-item>
                                                <q-item-section side class="subtitle">Date of next Increment:</q-item-section>
                                                <q-item-section class="text-label">
                                                    {{ formatDate(data.remuneration_detail.next_increment_date) }}
                                                </q-item-section>
                                            </q-item>
                                        </q-list>
                                    </q-card-section>
                                </q-card>
<!--                                Transfer-->
                                <q-card class="q-mt-md">
                                    <div class="flex items-center justify-between q-pa-md bg-white">
                                        <div>
                                            <div class="stitle">Transfer History</div>
                                        </div>

                                    </div>

                                    <q-card-section>
                                        <div v-if="sortedTransfers.length > 0">
                                            <q-timeline color="primary" class="q-pa-md">
                                                <q-timeline-entry
                                                    v-for="(transfer, index) in sortedTransfers"
                                                    :key="transfer.id"
                                                    :title="formatDate(transfer.transfer_date)"
                                                    :icon="index === sortedTransfers.length - 1 ? 'flag' : 'swap_horiz'"
                                                >
                                                    <template v-slot:subtitle>
                                                        <div class="flex items-center justify-between">
                                                            <span>Transfer #{{ index + 1 }}</span>
                                                        </div>
                                                    </template>

                                                    <div>
                                                        From
                                                        <strong>{{ transfer.old_office?.name || 'Unknown Office' }}</strong>
                                                        to
                                                        <strong>{{ transfer.new_office?.name || 'Unknown Office' }}</strong>
                                                    </div>
                                                    <div v-if="transfer.supporting_document">
                                                        Transfer Order:
                                                        <q-btn
                                                            dense
                                                            flat
                                                            color="primary"
                                                            icon="visibility"
                                                            :href="`/storage/${transfer.supporting_document}`"
                                                            target="_blank"
                                                        />
                                                        <q-btn
                                                            dense
                                                            flat
                                                            color="primary"
                                                            icon="download"
                                                            :href="`/storage/${transfer.supporting_document}`"
                                                            target="_blank"
                                                            download
                                                        />
                                                    </div>
                                                </q-timeline-entry>
                                            </q-timeline>
                                        </div>

                                        <div v-else class="text-grey-7 text-center q-pa-md italic">
                                            No transfer history found
                                        </div>
                                    </q-card-section>

                                </q-card>
                            </div>

                            <div class="col-12 col-md-6">
<!--                                EngagementCard-->
                                <q-card class="q-mt-md">
                                    <div class="flex items-center justify-between q-pa-md bg-white">
                                        <div>
                                            <div class="stitle">Engagement Cards</div>
                                        </div>
                                    </div>
                                    <q-card-section>
                                        <div v-if="data.engagement_card && data.engagement_card.length">
                                            <q-item
                                                v-for="card in data.engagement_card"
                                                :key="card.id"
                                                class="q-mb-md"
                                            >
                                                <!-- Stack Card No and Fiscal vertically -->
                                                <q-item-section>
                                                    <div class="subtitle">Card No: {{ card.card_no }}</div>
                                                    <div class="text-label">Fiscal: {{ card.fiscal_year }}</div>
                                                </q-item-section>

                                                <!-- Buttons aligned horizontally on the right -->
                                                <q-item-section side>
                                                    <div class="row items-center q-gutter-sm">
                                                        <q-btn
                                                            dense
                                                            color="primary"
                                                            icon="download"
                                                            @click="downloadPdf(card)"
                                                        />
                                                    </div>
                                                </q-item-section>
                                            </q-item>
                                        </div>

                                        <div v-else class="text-grey-7 text-center q-pa-md italic">
                                            No engagement card generated.
                                        </div>
                                    </q-card-section>
                                </q-card>

<!--                                Deletion-->
                                <Deletion v-if="data.employment_type === 'Deleted'" :deletion="data.deletion_detail" :canEditDelete="canEditDelete"/>
                            </div>

                        </div>



                    </div>

                </div>
            </q-card-section>
        </q-card>

    </q-page>
</template>

<script setup>
import FrontendLayout from "@/Layouts/FrontendLayout.vue";
import {computed, ref} from "vue";
import useUtils from "../../../Compositions/useUtils.js";
import axios from "axios";

const {pay_matrix,formatDate} = useUtils();

defineOptions({
    layout: FrontendLayout
})

const props=defineProps(['data',]);
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

const sortedTransfers = computed(() => {
    return [...(props.data?.transfers || [])].sort((a, b) =>
        new Date(a.transfer_date) - new Date(b.transfer_date)
    )
})


const downloadPdf = async (card) => {
    try {
        const response = await axios.get(route('bio.download-engagement-card', card.id), {
            responseType: 'blob',
        });
        const url = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `EngagementCard_${card.card_no}.pdf`);
        document.body.appendChild(link);
        link.click();
        link.remove();
    } catch (error) {
        $q.notify({ type: 'negative', message: 'Failed to download PDF.' });
    }
};
const handleBack=e=>{
    window.history.back();
}

</script>
