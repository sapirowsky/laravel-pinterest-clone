<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import vueFilePond, { setOptions } from "vue-filepond";
import "filepond/dist/filepond.min.css";
import FilePondPluginFileValidateType from "filepond-plugin-file-validate-type";
import FilepondPluginFileValidateSize from "filepond-plugin-file-validate-size";
import { onMounted, ref } from "vue";
import Pin from "@/Components/Pin.vue";

import TrashBin from "@/Components/TrashBin.vue";
const serverMessage = ref({});

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
    images.value.unshift(JSON.parse(file.serverId));
};
const filepondInitialized = () => {
    console.log("filepond ready");
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
            <div class="container mx-auto sm:px-6 lg:px-8">
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
                            <div class="mt-8 mb-4">
                                <h3 class="text-2xl font-medium text-center">
                                    Galeria pinów
                                </h3>
                                <div class="pin-showcase">
                                    <Pin
                                        v-for="image in images"
                                        :key="image.id"
                                        :index="image.id"
                                        :imageOwner="true"
                                        :url="image.name"
                                        alt="zdjęcie"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
    <footer
        class="w-full sm:flex sm:justify-center bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400"
    >
        <div class="container px-2 py-4">
            Logo 2023, wszystkie prawa zastrzeżone.
        </div>
    </footer>
</template>

<style scoped>
.pin-showcase {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    justify-content: center;
    gap: 16px;
}
</style>
