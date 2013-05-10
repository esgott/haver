package hu.esgott.haver.algorythm;

import java.util.List;

public interface MessageOrderingAlgorythm {

	List<Message> orderMessages(List<Message> messages);

}
