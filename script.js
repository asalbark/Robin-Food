const chatbotToggler = document.querySelector(".chatbot-toggler");
const closeBtn = document.querySelector(".close-btn");
const chatbox = document.querySelector(".chatbox");
const chatInput = document.querySelector(".chat-input textarea");
const sendChatBtn = document.querySelector(".chat-input span");

let userMessage = null;
const inputInitHeight = chatInput.scrollHeight;

const createChatLi = (message, className) => {
  const chatLi = document.createElement("li");
  chatLi.classList.add("chat", `${className}`);
  let chatContent = className === "outgoing" ? `<p>${message}</p>` : `<span class="material-symbols-outlined">smart_toy</span><p>${message}</p>`;
  chatLi.innerHTML = chatContent;
  return chatLi;
}

const displayAnswer = (questionNumber) => {
  let answer = "";
  switch(questionNumber) {
    case 1:
      answer = "יש בדף שאתה נמצא בו כפתור בשף שיתוף אוכל, לוחצים על הכפתור ואז אפשר לשתף אוכל.";
      break;
    case 2:
      answer = "אפשר לקבל אוכל דרך לחיצה על כפתור 'קבלת אוכל', בוחרים איזה אוכל רוצים מוסיפים לעגלה ואז מאשרים קבלה.";
      break;
    case 3:
      answer = "בדף 'שיתוף אוכל' יש בתפריט כתפור 'שיתופים קודמים'.";
      break;
    case 4:
      answer = "באתר אפשר לשתף ולקבל אוכל. אפשר לצפות בשיתופים קודמים.";
      break;
    default:
      answer = "לא מצאתי תשובה לשאלתך";
  }
  chatbox.appendChild(createChatLi(answer, "incoming"));
  chatbox.scrollTo(0, chatbox.scrollHeight);
}

const handleChat = () => {
  userMessage = chatInput.value.trim();
  if (!userMessage) return;
  
  const questionNumber = parseInt(userMessage);
  if (questionNumber >= 1 && questionNumber <= 4) {
    chatbox.appendChild(createChatLi(userMessage, "outgoing"));
    chatbox.scrollTo(0, chatbox.scrollHeight);
    displayAnswer(questionNumber);
  } else {
    chatbox.appendChild(createChatLi("שגיאה! בחר מספר מ 1 עד 4", "outgoing"));
    chatbox.scrollTo(0, chatbox.scrollHeight);
  }

  chatInput.value = "";
}

chatInput.addEventListener("input", () => {
  chatInput.style.height = `${inputInitHeight}px`;
  chatInput.style.height = `${chatInput.scrollHeight}px`;
});

chatInput.addEventListener("keydown", (e) => {
  if (e.key === "Enter" && !e.shiftKey && window.innerWidth > 800) {
    e.preventDefault();
    handleChat();
  }
});

sendChatBtn.addEventListener("click", handleChat);
closeBtn.addEventListener("click", () => document.body.classList.remove("show-chatbot"));
chatbotToggler.addEventListener("click", () => document.body.classList.toggle("show-chatbot"));
