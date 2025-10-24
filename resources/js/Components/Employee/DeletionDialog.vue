<template>

    <q-dialog v-model="show">
      <q-card style="min-width: 400px;">
        <q-card-section class="row items-center q-pa-sm bg-negative text-white">
          <div class="text-h6">Employee Deletion Details</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>

        <q-form @submit.prevent="submitForm">
          <q-card-section class="q-gutter-md">
              <q-select
                  v-model="form.reason"
                  label="Reason"
                  outlined
                  dense
                  required
                  :options="[
                        'Expired',
                        'Resigned',
                        'Dismissed',
                        'Regularised',
                        'Others (Specify the reasons to Remarks)',
                        'Overage (Retired)'
                      ]"
                  :error="!!form.errors.reason"
                  :error-message="form.errors.reason"
                  :rules="[
                        val => !!val || 'Reason is required'
                     ]"
              />
            <q-input v-model="form.seniority_list" label="Seniority List" dense outlined type="number" />
            <q-input v-model="form.year" label="Year" dense outlined type="number" />
            <q-input v-model="form.remark" label="Remark" type="textarea" dense outlined />

              <q-file v-model="form.supporting_document" label="Supporting Document"  dense outlined required
                    :error="!!form.errors.supporting_document"
                    :error-message="form.errors.supporting_document"
                    :rules="[
                        val => !!val || 'Supporting Document is required'
                     ]"
            />
          </q-card-section>

          <q-card-actions align="right" class="q-pa-sm">
            <q-btn flat label="Cancel" v-close-popup />
            <q-btn color="negative" label="Submit Deletion" type="submit" />
          </q-card-actions>
        </q-form>
      </q-card>
    </q-dialog>


</template>

<script setup>
import { ref, watch } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import {useQuasar} from "quasar";
const $q = useQuasar()

const page = usePage();
const props = defineProps({
  modelValue: Boolean,
  employee: Object,
})
const emit = defineEmits(['update:modelValue'])

const show = ref(props.modelValue)

watch(() => props.modelValue, val => show.value = val)

watch(show, val => emit('update:modelValue', val))

const form = useForm({
  seniority_list:'',
  reason: '',
  year: new Date().getFullYear(),
  remark: '',
  supporting_document: null,
})
function submitForm() {
    form.post(route('deletion.store', props.employee), {
        preserveScroll: true,
        onSuccess: () => {
            show.value = false
            form.reset()

            const msg = page.props.flash.success

            if (msg === 'Employee moved to Trash.') {
                $q.notify({
                    type: 'negative',
                    message: 'Employee Moved to trash.'
                })
            } else {
                $q.notify({
                    type: 'positive',
                    message: 'Employee Deleted successfully.'
                })
            }
        }
    })
}
// function submitForm() {
//   form.post(route('deletion.store', props.employee), {
//     preserveScroll: true,
//     onSuccess: () => {
//       show.value = false
//       form.reset()
//         $q.notify({
//             type: 'positive',
//             message: 'Employee Deleted successfully.'
//         })
//     }
//   })
// }
</script>
