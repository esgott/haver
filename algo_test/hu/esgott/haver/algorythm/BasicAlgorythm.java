package hu.esgott.haver.algorythm;

import java.util.Collections;
import java.util.Comparator;
import java.util.List;

public class BasicAlgorythm implements MessageOrderingAlgorythm {

	private double messageScoreWeight = 1;
	private double authorScoreWeight = 1;
	private double groupScoreWeight = 1;

	public BasicAlgorythm() {
	}

	public BasicAlgorythm(double messageScoreWeight, double authorScoreWeight, double groupScoreWeight) {
		this.messageScoreWeight = messageScoreWeight;
		this.authorScoreWeight = authorScoreWeight;
		this.groupScoreWeight = groupScoreWeight;
	}

	@Override
	public List<Message> orderMessages(List<Message> messages) {
		Collections.sort(messages, new Comparator<Message>() {

			@Override
			public int compare(Message message1, Message message2) {
				double score1 = messageScore(message1);
				double score2 = messageScore(message2);

				return Double.compare(score2, score1);
			}

		});

		return messages;
	}

	private double messageScore(Message message) {
		double messageScore = messageScoreWeight * message.score;
		double authorScore = authorScoreWeight * message.author.score;
		double groupScore = groupScoreWeight * message.group.score;

		return messageScore + authorScore + groupScore;
	}

}
