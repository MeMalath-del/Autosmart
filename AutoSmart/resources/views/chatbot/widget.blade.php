<div id="chatbot-widget" class="position-fixed" style="bottom:20px;left:20px;z-index:9999;">
    <button id="chatbot-toggle" class="btn btn-primary rounded-circle shadow-lg" style="width:60px;height:60px;">
        <i class="bi bi-chat-dots fs-4"></i>
    </button>
    
    <div id="chatbot-container" class="d-none bg-white rounded shadow-lg overflow-hidden" style="width:350px;height:500px;">
        <div class="bg-primary text-white p-3 d-flex justify-content-between align-items-center">
            <div><h6 class="mb-0"><i class="bi bi-robot me-2"></i>مساعد AutoSmart</h6><small>متصل الآن</small></div>
            <button id="chatbot-close" class="btn btn-link text-white p-0"><i class="bi bi-x-lg"></i></button>
        </div>
        
        <div id="chatbot-messages" class="p-3" style="height:370px;overflow-y:auto;">
            <div class="text-center text-muted py-4">
                <i class="bi bi-chat-dots display-4"></i>
                <p class="mt-2">مرحباً! كيف يمكنني مساعدتك؟</p>
            </div>
        </div>
        
        <div class="border-top p-2">
            <form id="chatbot-form" class="d-flex gap-2">
                <input type="text" id="chatbot-input" class="form-control" placeholder="اكتب رسالتك...">
                <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i></button>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('chatbot-toggle').onclick = () => {
    document.getElementById('chatbot-container').classList.toggle('d-none');
    document.getElementById('chatbot-toggle').classList.toggle('d-none');
};
document.getElementById('chatbot-close').onclick = () => {
    document.getElementById('chatbot-container').classList.add('d-none');
    document.getElementById('chatbot-toggle').classList.remove('d-none');
};
document.getElementById('chatbot-form').onsubmit = async (e) => {
    e.preventDefault();
    const input = document.getElementById('chatbot-input');
    const msg = input.value.trim();
    if (!msg) return;
    
    const messages = document.getElementById('chatbot-messages');
    messages.innerHTML += `<div class="d-flex justify-content-end mb-2"><div class="bg-primary text-white p-2 rounded" style="max-width:80%">${msg}</div></div>`;
    input.value = '';
    messages.scrollTop = messages.scrollHeight;
    
    const res = await fetch('{{ route("chatbot.message") }}', {
        method: 'POST', headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        body: JSON.stringify({message: msg})
    });
    const data = await res.json();
    messages.innerHTML += `<div class="d-flex mb-2"><div class="bg-light p-2 rounded" style="max-width:80%">${data.message.replace(/\n/g, '<br>')}</div></div>`;
    messages.scrollTop = messages.scrollHeight;
};
</script>
