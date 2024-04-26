<template>
    <div
        class="block min-h-[16rem] w-full rounded-md border-0 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus-within:ring-2 focus-within:ring-inset focus-within:ring-blue-600 sm:text-sm sm:leading-6"
    >
        <div v-if="editor" class="flex w-full border-b border-gray-300">
            <TipTapButton
                v-for="(button, index) in buttonData"
                :key="index"
                :icon="button.icon"
                :isActive="
                    button.isActiveCheck ? editor.isActive(button.isActiveCheck, button.isActiveCheckParams) : false
                "
                @click="
                    editor
                        .chain()
                        .focus()
                        [button.action](button.actionParams || {})
                        .run()
                "
                :disabled="
                    editor
                        .can()
                        .chain()
                        .focus()
                        [button.action](button.actionParams || {})
                        .run() === false
                "
                :class="{
                    'rounded-tl-md': index === 0,
                    'rounded-tr-md': index === buttonData.length - 1,
                    '-ml-px': index !== 0,
                }"
            />
        </div>
        <EditorContent :editor="editor" class="w-full px-3 py-2" />
    </div>
</template>

<script setup>
import { ref, watch, onMounted, onBeforeUnmount } from 'vue';
import StarterKit from '@tiptap/starter-kit';
import { Editor, EditorContent } from '@tiptap/vue-3';
import TipTapButton from '@/Components/TipTapButton.vue';

const props = defineProps({
    context: Object,
});

const editor = ref(null);

const buttonData = ref([
    { icon: 'bi-type-bold', action: 'toggleBold', isActiveCheck: 'bold' },
    { icon: 'bi-type-italic', action: 'toggleItalic', isActiveCheck: 'italic' },
    {
        icon: 'bi-type-strikethrough',
        action: 'toggleStrike',
        isActiveCheck: 'strike',
    },
    {
        icon: 'bi-type-h1',
        action: 'toggleHeading',
        actionParams: { level: 1 },
        isActiveCheck: 'heading',
        isActiveCheckParams: { level: 1 },
    },
    {
        icon: 'bi-type-h2',
        action: 'toggleHeading',
        actionParams: { level: 2 },
        isActiveCheck: 'heading',
        isActiveCheckParams: { level: 2 },
    },
    {
        icon: 'bi-type-h3',
        action: 'toggleHeading',
        actionParams: { level: 3 },
        isActiveCheck: 'heading',
        isActiveCheckParams: { level: 3 },
    },
    {
        icon: 'bi-list-ul',
        action: 'toggleBulletList',
        isActiveCheck: 'bulletList',
    },
    {
        icon: 'bi-list-ol',
        action: 'toggleOrderedList',
        isActiveCheck: 'orderedList',
    },
    {
        icon: 'bi-blockquote-left',
        action: 'toggleBlockquote',
        isActiveCheck: 'blockquote',
    },
]);

watch(
    () => props.context?._value,
    (value) => {
        const isSame = editor.value?.getHTML() === value;
        if (!isSame) {
            editor.value.commands.setContent(value, false);
        }
    },
);

function handleInput(value) {
    props.context.node.input(value);
}

onMounted(() => {
    editor.value = new Editor({
        extensions: [StarterKit],
        content: props.context?._value,
        editorProps: {
            attributes: {
                class: 'prose focus:outline-none ',
            },
        },
        onUpdate: () => {
            handleInput(editor.value.getHTML());
        },
    });
});

onBeforeUnmount(() => {
    editor.value?.destroy();
});
</script>
