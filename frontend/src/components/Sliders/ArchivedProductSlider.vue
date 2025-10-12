<script setup>
import { ref, onMounted, onUnmounted, computed, defineProps, defineEmits } from "vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faGear, faPen, faEye, faTrash, faTrashCanArrowUp } from "@fortawesome/free-solid-svg-icons";

const showMenu = ref(false);
const buttonRef = ref(null);
const menuRef = ref(null);
const menuPosition = ref({ top: 0, left: 0 });

const props = defineProps({
    product_id: String,
});
const emit = defineEmits(["deleteProduct", 'restore']);

const toggleMenu = () => {
    showMenu.value = !showMenu.value;
    if (showMenu.value && buttonRef.value) {
        const rect = buttonRef.value.getBoundingClientRect();
        menuPosition.value = {
            top: rect.bottom + 6,
            left: rect.right - 180,
        };
    }
};

// Close menu when clicking outside
const handleClickOutside = (event) => {
    const button = buttonRef.value;
    const menu = menuRef.value;

    if (
        showMenu.value &&
        menu &&
        !menu.contains(event.target) &&
        button &&
        !button.contains(event.target)
    ) {
        showMenu.value = false;
    }
};

onMounted(() => {
    document.addEventListener("click", handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener("click", handleClickOutside);
});

const handleDelete = async (id) => {
    await emit("deleteProduct", id);
};

const handleRestore = async (id) => {
    await emit('restoreProduct', id)
}

const menuStyle = computed(() => ({
    top: `${menuPosition.value.top}px`,
    left: `${menuPosition.value.left}px`,
}));
</script>


<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.15s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
<template>
    <div class="relative inline-flex items-center justify-center">
        <!-- Gear Button -->
        <button ref="buttonRef"
            class="flex items-center justify-center w-9 h-9 bg-gray-100 border border-gray-200 rounded-full shadow-sm hover:bg-gray-200 focus:ring-2 focus:ring-blue-400 transition-all duration-150"
            @click.stop="toggleMenu">
            <FontAwesomeIcon :icon="faGear" class="text-gray-600" />
        </button>

        <!-- Teleported Dropdown Menu -->
        <teleport to="body">
            <transition name="fade">
                <div v-if="showMenu" ref="menuRef"
                    class="fixed w-44 bg-white rounded-xl shadow-xl border border-gray-100 z-[9999] overflow-hidden"
                    :style="menuStyle">

                    <button
                        class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-red-50 transition-colors duration-150"
                        @click="handleRestore(props.product_id)">
                        <FontAwesomeIcon :icon="faTrashCanArrowUp" class="mr-2 text-green-500" /> Restore
                    </button>

                    <button
                        class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-red-50 transition-colors duration-150"
                        @click="handleDelete(props.product_id)">
                        <FontAwesomeIcon :icon="faTrash" class="mr-2 text-red-500" /> Delete
                    </button>
                </div>
            </transition>
        </teleport>
    </div>
</template>