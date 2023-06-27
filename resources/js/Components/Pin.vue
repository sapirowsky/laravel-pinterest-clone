<script setup>
import { Head, Link } from "@inertiajs/vue3";
import { ref } from "vue";
import TrashBin from "@/Components/TrashBin.vue";
import Download from "@/Components/Download.vue";
defineProps({
    url: String,
    alt: String,
    index: Number,
    canDelete: Boolean,
    imageOwner: Boolean,
});
const biggerImage = ref(false);
const toggleBiggerImage = () => {
    biggerImage.value = !biggerImage.value;
};
const show = ref(false);
const imageToDelete = ref("");
const imageToDeleteIndex = ref();
const openDeletion = (url, index) => {
    show.value = true;
    imageToDelete.value = url;
    imageToDeleteIndex.value = index;
};
const close = () => {
    show.value = false;
};
const deleteImage = () => {
    axios
        .post("/delete-image", {
            imageId: imageToDeleteIndex.value,
        })
        .then(() => {
            close();
            location.reload();
        });
};
const whatSize = (index) => {
    if (index % 2 === 0) {
        return "small";
    }
    if (index % 3 === 0 || index === 0) {
        return "medium";
    }
    return "large";
};
</script>

<template>
    <div :class="whatSize(index)" class="relative">
        <img
            class="rounded-lg w-full h-full object-cover"
            :src="'./storage/images/' + url"
            :alt="alt"
            @click="toggleBiggerImage"
        />
        <div class="absolute top-1 right-1 flex gap-1 flex-wrap">
            <a
                v-if="index"
                :href="'./download/' + url"
                class="rounded-full w-8 h-8 bg-gray-400 hover:bg-gray-500 grid place-items-center hover:cursor-pointer"
            >
                <Download class="w-4/5 h-4/5 text-white" />
            </a>
            <span
                v-if="canDelete || imageOwner"
                class="rounded-full w-8 h-8 bg-red-500 hover:bg-red-600 grid place-items-center hover:cursor-pointer"
                @click="openDeletion(url, index)"
            >
                <TrashBin class="w-4/5 h-4/5 text-white" />
            </span>
        </div>
    </div>
    <teleport to="body" v-if="biggerImage">
        <div
            class="fixed inset-0 backdrop-blur-sm"
            @click="toggleBiggerImage"
        ></div>
        <div class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2">
            <div
                class="flex flex-col gap-2 max-h-screen max-w-screen-md bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 container px-2 py-4 rounded-xl"
            >
                <div class="flex gap-2 justify-between">
                    <h1 class="text-xl font-semibold">{{ url }}</h1>
                    <h1
                        class="text-xl font-semibold text-red-500 hover:text-red-600 hover:cursor-pointer"
                        @click="toggleBiggerImage"
                    >
                        X
                    </h1>
                </div>
                <div class="grid place-content-center">
                    <img
                        class="object-cover w-full rounded-xl"
                        :src="'/storage/images/' + url"
                        alt="zdjecie"
                    />
                </div>
            </div>
        </div>
    </teleport>
    <teleport to="body" v-if="show">
        <transition leave-active-class="duration-200">
            <div
                v-show="show"
                class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50 backdrop-blur-sm grid place-content-center"
                scroll-region
            >
                <transition
                    enter-active-class="ease-out duration-300"
                    enter-from-class="opacity-0"
                    enter-to-class="opacity-100"
                    leave-active-class="ease-in duration-200"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <div
                        class="bg-gray-800 text-gray-600 dark:text-gray-400 p-8 rounded-xl"
                    >
                        <div class="flex justify-between">
                            <h1 class="text-2xl font-semibold">
                                Usuwanie zdjęcia
                            </h1>
                            <span
                                class="text-2xl font-extrabold hover:cursor-pointer"
                                @click="close"
                                >X</span
                            >
                        </div>
                        <p>Czy jesteś pewny że chcesz usunąć to zdjęcie?</p>
                        <img
                            class="max-h-96 w-full object-cover rounded-lg"
                            v-if="imageToDelete !== ''"
                            :src="'./storage/images/' + imageToDelete"
                            alt="zdjecie"
                        />
                        <div class="w-full flex justify-end mt-4 gap-4">
                            <div
                                class="py-2 px-4 hover:cursor-pointer bg-gray-400 hover:bg-gray-600 text-black rounded"
                                @click="close"
                            >
                                Anuluj
                            </div>
                            <div
                                class="py-2 px-4 hover:cursor-pointer bg-red-500 hover:bg-red-600 text-white rounded"
                                @click="deleteImage()"
                            >
                                Potwierdź
                            </div>
                        </div>
                    </div>
                </transition>
            </div>
        </transition>
    </teleport>
</template>

<style scoped>
.small {
    grid-row-end: span 26;
}
.medium {
    grid-row-end: span 33;
}
.large {
    grid-row-end: span 40;
}
</style>
