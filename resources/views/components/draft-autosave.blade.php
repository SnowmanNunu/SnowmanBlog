<div x-data="{
    key: 'snowmanblog_post_draft_{{ request()->route('record') ? 'edit_' . request()->route('record')->id : 'create' }}',
    hasDraft: false,
    init() {
        const draft = localStorage.getItem(this.key);
        this.hasDraft = !!draft;
        setInterval(() => {
            localStorage.setItem(this.key, JSON.stringify(this.$wire.data));
            this.hasDraft = true;
        }, 5000);
    },
    restoreDraft() {
        const draft = localStorage.getItem(this.key);
        if (!draft) return;
        try {
            const data = JSON.parse(draft);
            Object.keys(data).forEach(field => {
                this.$wire.set('data.' + field, data[field]);
            });
        } catch (e) {}
    },
    clearDraft() {
        localStorage.removeItem(this.key);
        this.hasDraft = false;
    }
}" x-init="init()" class="text-sm">
    <template x-if="hasDraft">
        <div class="flex items-center gap-2 text-amber-600 bg-amber-50 dark:bg-amber-900/20 dark:text-amber-400 px-3 py-2 rounded-lg border border-amber-100 dark:border-amber-900/30">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
            <span>检测到未保存的草稿</span>
            <button type="button" @click="restoreDraft()" class="font-medium underline hover:text-amber-700 dark:hover:text-amber-300">恢复</button>
            <button type="button" @click="clearDraft()" class="font-medium underline hover:text-amber-700 dark:hover:text-amber-300">清除</button>
        </div>
    </template>
</div>
