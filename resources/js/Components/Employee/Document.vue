

<template>
    <q-card class="q-mt-md">
        <q-card-section>
            <div v-if="filteredDocuments.length === 0">
                <div class="stitle text-lg font-bold">Uploaded Documents</div>
                <p class="text-center text-grey py-6">No documents found</p>

            </div>

            <div v-if="filteredDocuments.length > 0">
                <div class="stitle text-lg font-bold">Uploaded Documents</div>

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
</template>

<script setup>
import {computed, ref} from "vue";

const props=defineProps(['data', 'office']);

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

function formatDate(dateStr) {
    const date = new Date(dateStr)
    return date.toLocaleDateString('en-IN', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}
</script>

<style scoped>

</style>
