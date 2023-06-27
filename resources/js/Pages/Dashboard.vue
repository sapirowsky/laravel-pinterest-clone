<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import vueFilePond, { setOptions } from "vue-filepond";
import "filepond/dist/filepond.min.css";
import FilePondPluginFileValidateType from "filepond-plugin-file-validate-type";
import FilepondPluginFileValidateSize from "filepond-plugin-file-validate-size";
import { onMounted, ref } from "vue";

import TrashBin from "@/Components/TrashBin.vue";
const serverMessage = ref({});
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

setOptions({
    server: {
        process: {
            url: "./upload",
            onerror: (res) => {
                serverMessage.value = JSON.parse(res);
            },
            headers: {
                "X-CSRF-TOKEN": document.head.querySelector(
                    'meta[name="csrf_token"]'
                ).content,
            },
        },
    },
    labelFileProcessingError: () => {
        return serverMessage.value.error;
    },
});

const FilePond = vueFilePond(
    FilePondPluginFileValidateType,
    FilepondPluginFileValidateSize
);

const pond = ref();
const images = ref([]);
const handleProcessedFile = (error, file) => {
    if (error) {
        console.log(error);
        return;
    }
    console.log(file.serverId);
    // images.value.unshift(file.serverId);
};
const filepondInitialized = () => {
    console.log("filepond ready");
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
onMounted(() => {
    axios
        .get("/current-user-images")
        .then((res) => {
            images.value = res.data;
        })
        .catch((err) => {
            console.log(err);
        });
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Twoje Piny
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="w-full">
                            <h1 class="text-3xl font-medium text-center">
                                Dodaj Piny
                            </h1>
                            <div class="mt-4">
                                <file-pond
                                    name="image"
                                    ref="pond"
                                    label-idle="Kliknij lub przerzuć pliki tutaj"
                                    :allow-multiple="true"
                                    @init="filepondInitialized"
                                    accepted-file-types="image/*"
                                    @processfile="handleProcessedFile"
                                    maxFileSize="64MB"
                                />
                            </div>
                            <div class="mt-8 mb-24">
                                <h3 class="text-2xl font-medium text-center">
                                    Galeria pinów
                                </h3>
                                <div class="pin-showcase">
                                    <div
                                        class="relative"
                                        v-for="image in images"
                                        :key="image.id"
                                    >
                                        <img
                                            class="w-full h-64 object-cover rounded-xl"
                                            :src="
                                                './storage/images/' + image.name
                                            "
                                            alt="zdjecie"
                                        />
                                        <div
                                            class="absolute rounded-full w-8 h-8 bg-red-500 hover:bg-red-600 top-1 right-1 grid place-items-center"
                                            @click="
                                                openDeletion(
                                                    image.name,
                                                    image.id
                                                )
                                            "
                                        >
                                            <TrashBin
                                                class="w-4/5 h-4/5 text-white"
                                            />
                                        </div>
                                    </div>
                                    <teleport to="body">
                                        <transition
                                            leave-active-class="duration-200"
                                        >
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
                                                        <div
                                                            class="flex justify-between"
                                                        >
                                                            <h1
                                                                class="text-2xl font-semibold"
                                                            >
                                                                Usuwanie zdjęcia
                                                            </h1>
                                                            <span
                                                                class="text-2xl font-extrabold hover:cursor-pointer"
                                                                @click="close"
                                                                >X</span
                                                            >
                                                        </div>
                                                        <p>
                                                            Czy jesteś pewny że
                                                            chcesz usunąć to
                                                            zdjęcie?
                                                        </p>
                                                        <img
                                                            class="max-h-96 w-full object-cover rounded-lg"
                                                            v-if="
                                                                imageToDelete !==
                                                                ''
                                                            "
                                                            :src="
                                                                './storage/images/' +
                                                                imageToDelete
                                                            "
                                                            alt="zdjecie"
                                                        />
                                                        <div
                                                            class="w-full flex justify-end mt-4 gap-4"
                                                        >
                                                            <div
                                                                class="py-2 px-4 hover:cursor-pointer bg-gray-400 hover:bg-gray-600 text-black rounded"
                                                                @click="close"
                                                            >
                                                                Anuluj
                                                            </div>
                                                            <div
                                                                class="py-2 px-4 hover:cursor-pointer bg-red-500 hover:bg-red-600 text-white rounded"
                                                                @click="
                                                                    deleteImage()
                                                                "
                                                            >
                                                                Potwierdź
                                                            </div>
                                                        </div>
                                                    </div>
                                                </transition>
                                            </div>
                                        </transition>
                                    </teleport>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.pin-showcase {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    justify-content: center;
    gap: 16px;
}
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
