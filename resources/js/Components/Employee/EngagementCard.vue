

<template>
    <q-card class="q-mt-md">


        <div class="flex items-center justify-between q-pa-md bg-white">
            <div>
                <div class="stitle ">Engagement Card</div>
            </div>

            <div class="flex q-gutter-sm">
                <q-btn
                    color="primary"
                    icon="edit"
                    @click="openDialog"
                />
            </div>
        </div>

        <q-card-section>
            <q-btn
                v-if="!data.engagement_card"
                color="primary"
                label="Preview"
                @click="openDialog"
            />

            <q-btn
                v-else
                color="primary"
                label="Download"
                @click="downloadPdf"
            />

            <q-dialog v-model="dialog" persistent full-width>
                <q-card>
                    <q-card-section class="row items-center justify-between bg-primary text-white">
                        <div class="text-h6">Engagement Card</div>
                        <q-btn dense flat icon="close" v-close-popup />
                    </q-card-section>

                    <q-card-section>
                        <q-editor
                            v-model="editorContent"
                            height="100%"
                            :definitions="{}"
                            :toolbar="[
                                 ['print', 'fullscreen'],
                            ]"

                        />
                    </q-card-section>

                    <q-card-actions align="right">
                        <q-btn color="primary" label="Save" @click="saveCard" />
                    </q-card-actions>
                </q-card>
            </q-dialog>
        </q-card-section>
    </q-card>
</template>


<script setup>
import { ref } from 'vue';
import { useQuasar } from 'quasar';

const props = defineProps({
    data: Object,
})

const dialog = ref(false);
const editorContent = ref('');
const $q = useQuasar();

const openDialog = async () => {
    dialog.value = true;
    try {
        axios.get(route('engagement-card.show', props.data))
            .then(res=>{
                editorContent.value = res.data;

            })
    } catch (err) {
        $q.notify({ type: 'negative', message: 'Failed to load card data' });
    }
};



const saveCard = async () => {
    try {
        await axios.post(route('engagement-card.store', props.data), {
            html_content: editorContent.value,
        });
        $q.notify({ type: 'positive', message: 'Card saved successfully!' });
        dialog.value = false;

    } catch (error) {
        $q.notify({ type: 'negative', message: 'Failed to save card.' });
    }
};

// Download PDF helper
const downloadPdf = async () => {
    try {
        // Request PDF from backend
        const response = await axios.get(route('engagement-card.download', props.data), {
            responseType: 'blob'  // important for binary response
        });

        // Create a blob link to download
        const url = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `EngagementCard_${props.data.id}.pdf`);
        document.body.appendChild(link);
        link.click();
        link.remove();

    } catch (error) {
        $q.notify({ type: 'negative', message: 'Failed to download PDF.' });
    }
};
</script>

<style scoped>

</style>
