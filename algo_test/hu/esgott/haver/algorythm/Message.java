package hu.esgott.haver.algorythm;

public class Message {
	public int score = 0;
	public Group group;
	public Author author;

	public Message(int score, Group group, Author author) {
		this.score = score;
		this.group = group;
		this.author = author;
	}

	@Override
	public String toString() {
		return "[score=" + score + " groupscore=" + group.score + " authorscore=" + author.score + "]";
	}
}
