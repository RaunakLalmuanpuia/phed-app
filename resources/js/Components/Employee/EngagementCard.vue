

<template>
    <q-card class="q-mt-md">
        <div class="flex items-center justify-between q-pa-md bg-white">
            <div>
                <div class="stitle">Engagement Card</div>
            </div>

            <div class="flex q-gutter-sm">
                <!-- Edit button only if card exists AND canStoreEngagementCard -->
                <q-btn
                    v-if="data.engagement_card && canCreateEngagementCard"
                    color="primary"
                    icon="edit"
                    @click="openDialog"
                />
            </div>
        </div>

        <q-card-section>
            <!-- Create button only if no card AND canStoreEngagementCard -->
            <q-btn
                v-if="!data.engagement_card && canCreateEngagementCard"
                color="primary"
                label="Create"
                @click="openDialog"
            />

            <!-- Download button only if card exists AND canDownloadEngagementCard -->
            <q-btn
                v-if="data.engagement_card && canDownloadEngagementCard"
                color="primary"
                label="Download"
                @click="downloadPdf"
            />

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
        </q-card-section>
    </q-card>
</template>



<script setup>
import { ref } from 'vue';
import { useQuasar } from 'quasar';
import {useForm} from "@inertiajs/vue3";

const props = defineProps({
    data: Object,
    canCreateEngagementCard: Boolean,
    canDownloadEngagementCard: Boolean,
})
const form = useForm({
    html_content: props.data?.engagement_card?.content || '',

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
                form.html_content = res.data;

            })
    } catch (err) {
        $q.notify({ type: 'negative', message: 'Failed to load card data' });
    }
};


const saveCard = () => {
    form.post(route('engagement-card.store', props.data), {
        preserveScroll: true,
        onSuccess: () => {
            dialog.value = false
            $q.notify({
                type: 'positive',
                message:  'Card saved successfully!'
            })
        }
    })
}

//
// const saveCard = async () => {
//     try {
//         await axios.post(route('engagement-card.store', props.data), {
//             html_content: editorContent.value,
//         });
//         $q.notify({ type: 'positive', message: 'Card saved successfully!' });
//         dialog.value = false;
//
//     } catch (error) {
//         $q.notify({ type: 'negative', message: 'Failed to save card.' });
//     }
// };

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
