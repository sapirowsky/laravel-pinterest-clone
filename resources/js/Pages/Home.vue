<script setup>
import { Head, Link } from "@inertiajs/vue3";
import Pin from "@/Components/Pin.vue";
import { onMounted, ref } from "vue";
defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});

const images = ref([]);
onMounted(() => {
    axios
        .get("/images")
        .then((res) => {
            images.value = res.data;
        })
        .catch((err) => {
            console.log(err);
        });
});
</script>

<template>
    <Head title="Tytuł" />

    <div
        class="relative sm:flex sm:flex-col sm:items-center min-h-screen bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white text-gray-600 dark:text-gray-400"
    >
        <header
            class="w-full sm:flex sm:justify-center bg-white dark:bg-gray-800"
        >
            <div class="container h-max flex justify-between py-4 px-2">
                <div
                    class="cursor-pointer hover:text-gray-900 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                >
                    <Link
                        class="text-2xl font-semibold text-gray-600 dark:text-gray-400"
                        href="/"
                        >Logo</Link
                    >
                </div>
                <div v-if="canLogin" class="flex items-center">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="hover:text-gray-900 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                    >
                        Hej,
                        <span class="font-semibold">
                            {{ $page.props.auth.user.name }}
                        </span>
                        !
                    </Link>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="font-semibold hover:text-gray-900 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                            >Zaloguj</Link
                        >

                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="ml-4 font-semibold hover:text-gray-900 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                            >Zarejestruj</Link
                        >
                    </template>
                </div>
            </div>
        </header>
        <main class="container my-8 px-2 pin-showcase">
            <Pin
                v-for="image in images"
                :key="image.id"
                :index="image.id"
                :canDelete="$page.props.auth.delete_any_image"
                :url="image.name"
                alt="zdjęcie"
            />
        </main>
        <footer
            class="w-full sm:flex sm:justify-center bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400"
        >
            <div class="container px-2 py-4">
                Logo 2023, wszystkie prawa zastrzeżone.
            </div>
        </footer>
    </div>
</template>

<style scoped>
.pin-showcase {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    justify-content: center;
    gap: 16px;
}
.bg-dots-darker {
    background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(0,0,0,0.07)'/%3E%3C/svg%3E");
}
@media (prefers-color-scheme: dark) {
    .dark\:bg-dots-lighter {
        background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(255,255,255,0.07)'/%3E%3C/svg%3E");
    }
}
</style>
