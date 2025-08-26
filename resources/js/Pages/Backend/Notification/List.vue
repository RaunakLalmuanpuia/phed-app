<script setup>
import BackendLayout from "@/Layouts/BackendLayout.vue";
import { date } from "quasar";
import { reactive, computed } from "vue";

defineOptions({
    layout: BackendLayout
});

// Props from Laravel
const props = defineProps({
    notifications: Array
});

const state = reactive({
    search: ""
});

// Filtered list
const filteredList = computed(() => {
    if (!state.search) return props.notifications;
    return props.notifications.filter((item) =>
        item.type.toLowerCase().includes(state.search.toLowerCase()) ||
        item.employee_name.toLowerCase().includes(state.search.toLowerCase()) ||
        item.office.toLowerCase().includes(state.search.toLowerCase())
    );
});

// Format date
const formatDateTime = (val) => date.formatDate(val, "D/M/YYYY hh:mm a");

// Navigation handler (you can replace alert with route redirection)
const goToShow = (id) => {
    alert(`Go to Notification Details Page (ID: ${id})`);
};
</script>

<template>
    <q-page padding class="container">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <div class="stitle">
                Notifications
            </div>
            <div class="flex q-gutter-sm">
                <q-input
                    v-model="state.search"
                    outlined
                    bg-color="white"
                    @keyup.enter="() => {}"
                    dense
                    placeholder="Search notifications..."
                />
            </div>
        </div>

        <q-separator class="q-my-sm" />

        <!-- Notification List -->
        <q-list>
            <q-item
                v-for="item in filteredList"
                :key="item.id"
                clickable
                class="q-mt-sm shadow-default bg-white q-pa-sm flex justify-between items-center"
                @click="$inertia.get(route('employee.show', item.employee_id))"
            >
                <q-item-section>
                    <q-item-label class="text-primary font-medium">
                        {{ item.type }}
                    </q-item-label>
                    <q-item-label>
                        Employee: <b>{{ item.employee_name }}</b>
                    </q-item-label>
                    <q-item-label caption>
                        Office: {{ item.office }}
                    </q-item-label>
                    <q-item-label caption>
                        Requested at: {{ formatDateTime(item.created_at) }}
                    </q-item-label>

                    <!-- 🔹 Show status if not pending -->
                    <q-item-label
                        v-if="item.status !== 'pending'"
                        caption
                        :class="{
                'text-green-600': item.status === 'approved',
                'text-red-600': item.status === 'rejected'
            }"
                    >
                        Status: {{ item.status }}
                    </q-item-label>
                </q-item-section>

                <q-item-section side>
                    <q-icon size="18px" name="chevron_right" />
                </q-item-section>
            </q-item>


            <!-- Empty state -->
            <div v-if="filteredList.length === 0" class="text-gray-500 text-center py-6">
                No pending requests found.
            </div>
        </q-list>
    </q-page>
</template>

<style scoped>
.stitle {
    font-size: 1.3rem;
    font-weight: 600;
}
</style>
