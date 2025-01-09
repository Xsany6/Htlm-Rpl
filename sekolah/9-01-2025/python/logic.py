import sys
import json

def check_answer(user_answer, correct_answer):
    user_answer = user_answer.strip().lower()
    correct_answer = correct_answer.strip().lower()
    return user_answer == correct_answer

if __name__ == "__main__":
    # Ambil input dari PHP dalam format JSON
    input_data = sys.stdin.read()
    data = json.loads(input_data)

    user_answer = data.get("user_answer", "")
    correct_answer = data.get("correct_answer", "")

    # Periksa jawaban
    is_correct = check_answer(user_answer, correct_answer)

    # Kirim hasil kembali ke PHP
    output = {"is_correct": is_correct}
    print(json.dumps(output))
