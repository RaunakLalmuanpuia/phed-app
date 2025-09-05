<template>
    <div>
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
                                    v-if="canCreateEngagementCard"
                                    dense
                                    color="primary"
                                    icon="edit"
                                    @click="openDialog(card)"
                                />

                                <q-btn
                                    v-if="canDownloadEngagementCard"
                                    dense
                                    color="primary"
                                    icon="download"
                                    @click="downloadPdf(card)"
                                />

                                <q-btn
                                    v-if="canDeleteEngagementCard"
                                    dense
                                    color="red"
                                    icon="delete"
                                    @click="$inertia.delete(route('engagement-card.destroy', card.id))"
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

        <!-- Dialog for editing/creating -->
        <q-dialog v-model="dialog" persistent>
            <q-card style="width: 1000px; max-width: 100vw;">
                <q-card-section class="row items-center justify-between bg-primary text-white">
                    <div class="text-h6">Engagement Card</div>
                    <q-btn dense flat icon="close" v-close-popup />
                </q-card-section>

                <q-card-section style="flex-grow: 1; overflow: auto; padding: 0;">
                    <q-editor
                        v-model="form.html_content"
                        style="width: 100%; max-height: 1100px;"
                    />
                </q-card-section>

                <q-card-actions align="right" style="flex-shrink: 0;">
                    <q-btn color="primary" label="Save" @click="saveCard" />
                </q-card-actions>
            </q-card>
        </q-dialog>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useQuasar } from 'quasar';
import { useForm } from "@inertiajs/vue3";
import axios from 'axios';

const props = defineProps({
    data: Object,
    canCreateEngagementCard: Boolean,
    canDownloadEngagementCard: Boolean,
    canDeleteEngagementCard: Boolean,
});

const dialog = ref(false);
const selectedCard = ref(null);
const form = useForm({
    html_content: '',
});

const $q = useQuasar();

// Open dialog for a specific card or a new card
const openDialog = async (card = null) => {
    selectedCard.value = card;
    if (card) {
        form.html_content = card.content;
    } else {
        form.html_content = '';
    }
    dialog.value = true;
};

// Save card (new or existing)
const saveCard = () => {


    form.post(route('engagement-card.update', selectedCard.value), {
        preserveScroll: true,
        onSuccess: () => {
            dialog.value = false;
            $q.notify({ type: 'positive', message: 'Card updated successfully!' });
        },
    });
};


// Download PDF for a specific card
const downloadPdf = async (card) => {
    try {
        const response = await axios.get(route('engagement-card.download', card.id), {
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
</script>
