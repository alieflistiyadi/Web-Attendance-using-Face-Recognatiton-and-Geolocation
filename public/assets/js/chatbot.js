document.addEventListener("DOMContentLoaded", () => {
    const chatBody = document.querySelector(".chat-body");
    const messageInput = document.querySelector(".message-input");
    const sendMessageButton = document.querySelector("#send-message");
    const fileInput = document.querySelector("#file-input");
    const fileUploadwrapper = document.querySelector(".file-upload-wrapper");
    const fileCancelButton = document.querySelector("#file-cancel");
    const chatbotToggler = document.querySelector("#chatbot-toggler");
    const closeChatbot = document.querySelector("#close-chatbot");
    const chatForm = document.querySelector(".chat-form");

    if (!chatbotToggler || !chatBody) return;

    // Deteksi otomatis: admin pakai /panel/chatbot, siswa pakai /chatbot
    const API_URL = window.location.pathname.startsWith("/panel")
        ? "/panel/chatbot"
        : "/chatbot";

    const userData = { message: null, file: { data: null, mime_type: null } };
    const chatHistory = [];

    const autoResizeTextarea = () => {
        messageInput.style.height = "auto";
        const newHeight = Math.min(messageInput.scrollHeight, 150);
        messageInput.style.height = newHeight + "px";
        chatForm.style.borderRadius =
            messageInput.scrollHeight > 47 ? "18px" : "32px";
    };

    messageInput.addEventListener("input", autoResizeTextarea);
    messageInput.addEventListener("paste", () =>
        setTimeout(autoResizeTextarea, 10),
    );

    const createMessageElement = (content, ...classes) => {
        const div = document.createElement("div");
        div.classList.add("message", ...classes);
        div.innerHTML = content;
        return div;
    };

    const generateBotResponse = async (incomingMessageDIV) => {
        const messageElement =
            incomingMessageDIV.querySelector(".message-text");
        chatHistory.push({ role: "user", content: userData.message });

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            const response = await fetch(API_URL, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                        ? csrfToken.getAttribute("content")
                        : "",
                },
                body: JSON.stringify({ message: userData.message }),
            });

            // Cek apakah response JSON (bukan redirect HTML ke login)
            const contentType = response.headers.get("content-type");
            if (!contentType || !contentType.includes("application/json")) {
                throw new Error(
                    "Sesi habis, silakan refresh halaman dan login kembali.",
                );
            }

            const data = await response.json();
            if (!response.ok)
                throw new Error(data.reply || "Terjadi kesalahan pada server.");

            messageElement.innerText = data.reply;
            messageElement.style.color = "";
            chatHistory.push({ role: "assistant", content: data.reply });
        } catch (error) {
            console.error(error);
            messageElement.innerText = "⚠️ " + error.message;
            messageElement.style.color = "#ff0000";
        } finally {
            userData.file = {};
            incomingMessageDIV.classList.remove("thinking");
            chatBody.scrollTo({
                top: chatBody.scrollHeight,
                behavior: "smooth",
            });
        }
    };

    const handleOutgoingMessage = (e) => {
        e.preventDefault();
        userData.message = messageInput.value.trim();
        if (!userData.message) return;

        fileUploadwrapper.classList.remove("file-uploaded");

        const outgoingContent = `<div class="message-text"></div>
            ${userData.file.data ? `<img src="data:${userData.file.mime_type};base64,${userData.file.data}" class="attachment" />` : ""}`;

        const outgoingMessageDIV = createMessageElement(
            outgoingContent,
            "user-message",
        );
        outgoingMessageDIV.querySelector(".message-text").textContent =
            userData.message;
        chatBody.appendChild(outgoingMessageDIV);
        chatBody.scrollTo({ top: chatBody.scrollHeight, behavior: "smooth" });

        messageInput.value = "";
        messageInput.style.height = "auto";
        chatForm.style.borderRadius = "32px";

        setTimeout(() => {
            const incomingContent = `
                <svg class="bot-avatar" xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 1024 1024">
                    <path d="M738.3 287.6H285.7c-59 0-106.8 47.8-106.8 106.8v303.1c0 59 47.8 106.8 106.8 106.8h81.5v111.1c0 .7.8 1.1 1.4.7l166.9-110.6 41.8-.8h117.4l43.6-.4c59 0 106.8-47.8 106.8-106.8V394.5c0-59-47.8-106.9-106.8-106.9zM351.7 448.2c0-29.5 23.9-53.5 53.5-53.5s53.5 23.9 53.5 53.5-23.9 53.5-53.5 53.5-53.5-23.9-53.5-53.5zm157.9 267.1c-67.8 0-123.8-47.5-132.3-109h264.6c-8.6 61.5-64.5 109-132.3 109zm110-213.7c-29.5 0-53.5-23.9-53.5-53.5s23.9-53.5 53.5-53.5 53.5 23.9 53.5 53.5-23.9 53.5-53.5 53.5zM867.2 644.5V453.1h26.5c19.4 0 35.1 15.7 35.1 35.1v121.1c0 19.4-15.7 35.1-35.1 35.1h-26.5zM95.2 609.4V488.2c0-19.4 15.7-35.1 35.1-35.1h26.5v191.3h-26.5c-19.4 0-35.1-15.7-35.1-35.1zM561.5 149.6c0 23.4-15.6 43.3-36.9 49.7v44.9h-30v-44.9c-21.4-6.5-36.9-26.3-36.9-49.7 0-28.6 23.3-51.9 51.9-51.9s51.9 23.3 51.9 51.9z"></path>
                </svg>
                <div class="message-text">
                    <div class="thinking-indicator">
                        <div class="dot"></div><div class="dot"></div><div class="dot"></div>
                    </div>
                </div>`;

            const incomingMessageDIV = createMessageElement(
                incomingContent,
                "bot-message",
                "thinking",
            );
            chatBody.appendChild(incomingMessageDIV);
            chatBody.scrollTo({
                top: chatBody.scrollHeight,
                behavior: "smooth",
            });
            generateBotResponse(incomingMessageDIV);
        }, 600);
    };

    messageInput.addEventListener("keydown", (e) => {
        if (e.key === "Enter" && !e.shiftKey && messageInput.value.trim()) {
            e.preventDefault();
            handleOutgoingMessage(e);
        }
    });

    sendMessageButton.addEventListener("click", (e) =>
        handleOutgoingMessage(e),
    );

    fileInput.addEventListener("change", () => {
        const file = fileInput.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = (e) => {
            fileUploadwrapper.querySelector("img").src = e.target.result;
            fileUploadwrapper.classList.add("file-uploaded");
            userData.file = {
                data: e.target.result.split(",")[1],
                mime_type: file.type,
            };
            fileInput.value = "";
        };
        reader.readAsDataURL(file);
    });

    fileCancelButton.addEventListener("click", () => {
        userData.file = {};
        fileUploadwrapper.classList.remove("file-uploaded");
    });

    document
        .querySelector("#file-upload")
        ?.addEventListener("click", () => fileInput.click());
    chatbotToggler.addEventListener("click", () =>
        document.body.classList.toggle("show-chatbot"),
    );
    closeChatbot.addEventListener("click", () =>
        document.body.classList.remove("show-chatbot"),
    );

    if (typeof EmojiMart !== "undefined") {
        const picker = new EmojiMart.Picker({
            theme: "light",
            skinTonePosition: "none",
            previewPosition: "none",
            onEmojiSelect: (emoji) => {
                const { selectionStart: start, selectionEnd: end } =
                    messageInput;
                messageInput.setRangeText(emoji.native, start, end, "end");
                messageInput.focus();
                setTimeout(autoResizeTextarea, 10);
            },
            onClickOutside: (e) => {
                if (e.target.id === "emoji-picker") {
                    document.body.classList.toggle("show-emoji-picker");
                } else {
                    document.body.classList.remove("show-emoji-picker");
                }
            },
        });
        document.querySelector(".chat-form")?.appendChild(picker);
    }

    autoResizeTextarea();
});
