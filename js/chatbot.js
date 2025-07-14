let messages = [
  { role: "system", content: "You are a helpful mental health assistant." }
];

function sendMessage() {
  const input = document.getElementById("user-input");
  const chatbox = document.getElementById("chatbox");
  const text = input.value.trim();
  if (!text) return;

  // Display user message
  const userDiv = document.createElement("div");
  userDiv.innerHTML = `<b>You:</b> ${text}`;
  chatbox.appendChild(userDiv);
  chatbox.scrollTop = chatbox.scrollHeight;

  messages.push({ role: "user", content: text });
  input.value = "";
  input.disabled = true;

  fetch("backend/chatbot.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ messages })
  })
  .then(res => res.json())
  .then(data => {
    const reply = data.choices[0].message.content;
    messages.push({ role: "assistant", content: reply });

    const botDiv = document.createElement("div");
    botDiv.innerHTML = `<b>Bot:</b> ${reply}`;
    chatbox.appendChild(botDiv);
    chatbox.scrollTop = chatbox.scrollHeight;
  })
  .catch(err => {
    const errorDiv = document.createElement("div");
    errorDiv.style.color = "red";
    errorDiv.innerText = "⚠️ Error: Chatbot unavailable.";
    chatbox.appendChild(errorDiv);
  })
  .finally(() => {
    input.disabled = false;
    input.focus();
  });
}
