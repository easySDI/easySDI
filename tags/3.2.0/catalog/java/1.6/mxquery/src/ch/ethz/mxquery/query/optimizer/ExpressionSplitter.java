/*   Copyright 2006 - 2009 ETH Zurich 
 *
 *   Licensed under the Apache License, Version 2.0 (the "License");
 *   you may not use this file except in compliance with the License.
 *   You may obtain a copy of the License at
 *
 *       http://www.apache.org/licenses/LICENSE-2.0
 *
 *   Unless required by applicable law or agreed to in writing, software
 *   distributed under the License is distributed on an "AS IS" BASIS,
 *   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *   See the License for the specific language governing permissions and
 *   limitations under the License.
 */
package ch.ethz.mxquery.query.optimizer;

import java.util.List;

import ch.ethz.mxquery.iterators.CompareIterator;
import ch.ethz.mxquery.iterators.LogicalIterator;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.opt.expr.CTerm;
import ch.ethz.mxquery.opt.expr.CompareLiteral;
import ch.ethz.mxquery.opt.expr.DTerm;
import ch.ethz.mxquery.opt.expr.Literal;
import ch.ethz.mxquery.opt.expr.LogicalUnit;
import ch.ethz.mxquery.opt.expr.ValueLiteral;

/**
 * This is an optimization class for the WindowIndexIterator. It helps to split a expression into disjunction terms
 * and finds max index patterns.
 * In the near future maybe this can replaced by a query rewriter.
 * @author Tim Kraska (<a href="mailto:mail@tim-kraska.de">mail@tim-kraska.de</a>)
 *
 */
public final class ExpressionSplitter {
	private List endVars;
	private List startVars;

	/**
	 * 
	 * @param startVars List of variables
	 * @param endVars List of variables
	 */
	public ExpressionSplitter(List startVars, List endVars) {
		super();
		// TODO Auto-generated constructor stub
		this.endVars = endVars;
		this.startVars = startVars;
	}

	/**
	 * Splits an Iterator tree into disjunction terms and sorts them regarding to the variable
	 * dependencies
	 * @param expr
	 * @return the DTerm containing the expression
	 */
	public DTerm splitExpression(XDMIterator expr){
		DTerm dnf = normalizeToDNF(expr);
		dnf.sort();
		return dnf;
	}
	
	/**
	 * Normalizes an iterator tree to disjunction terms
	 * @param expr
	 * @return the normalized DTern for expr 
	 */
	private DTerm normalizeToDNF(XDMIterator expr){
		DTerm result = new DTerm();
		//expr = removeBooleanIterator(expr);
		LogicalIterator lExpr;
		if((lExpr = getLogical(expr))!= null){
			DTerm left = normalizeToDNF(lExpr.getLeftChild());
			DTerm right = normalizeToDNF(lExpr.getRightChild());
			if(lExpr.getOperatorType() == LogicalIterator.AND){
				result = left.combineLists(right);
			}else{
				left.addAll(right);
				result = left;
			}
		}else{
			CTerm term = new CTerm();
			term.addLiteral(createTerm(expr));
			result.addCTerm(term);
		}
		return result;
	}
	
	/**
	 * Creates literal term consisting of a iterator 
	 * @param expr 
	 * @return
	 */
	private Literal createTerm(XDMIterator expr){
		//expr = removeBooleanIterator(expr);
		CompareIterator cExpr = getCompareIterator(expr);
		if(cExpr != null){
			return createCompareTerm(cExpr, startVars, endVars);
		}else{
			return createLiteralTerm(expr, startVars, endVars);
		}
	}
	
	/**
	 * Factory to create a new Literal from a Iterator and analyze the dependencies
	 * to the start and end variables
	 * @param iter
	 * @param startVars
	 * @param endVars
	 * @return
	 */
	private Literal createLiteralTerm(XDMIterator iter, List startVars, List endVars){
		Literal term = new Literal(iter);
		TermVisitor visitor = new TermVisitor(term, startVars, endVars);
		term.setDependency(visitor.getDependency());
		return term;
	}
	
	/**
	 * Creates a new compare literal
	 * @param iter
	 * @param startVars
	 * @param endVars
	 * @return
	 */
	private Literal createCompareTerm(CompareIterator iter, List startVars, List endVars){
		ValueLiteral leftLiteral = createValueTerm(iter.getLeftPart(), startVars, endVars);
		ValueLiteral rightLiteral = createValueTerm(iter.getRightPart(), startVars, endVars);

		CompareLiteral term = new CompareLiteral(iter, iter.getComparator(), iter.getCompareType(), leftLiteral, rightLiteral);
		return term;
	}
	
	/**
	 * Helper calls to create a new value term from an iterator and analyze the dependencies
	 * @param iter
	 * @param startVars
	 * @param endVars
	 * @return the value literal for the iterator 
	 */
	public static ValueLiteral createValueTerm(XDMIterator iter, List startVars, List endVars){
		ValueLiteral term = new ValueLiteral(iter);
		TermVisitor visitor = new TermVisitor(term, startVars, endVars);
		term.setDependency(visitor.getDependency());
		term.setColumnName(visitor.getColumnName());
		if(term.getDependency() == LogicalUnit.DEPENDENCY_STARTEND){
			term.setIndexable(LogicalUnit.INDEX_IMPOSSIBLE);
		}
		return term;
	}
	
	
	/**
	 * Checks and returns if the iterator is a compare iterator
	 * @param expr
	 * @return
	 */
	private CompareIterator getCompareIterator(XDMIterator expr){
		if(expr instanceof CompareIterator){
			return (CompareIterator) expr;
		}else{
			return null;
		}
	}
	
	/**
	 * Checks and returns if the iterator is a logical iterator
	 * @param expr
	 * @return
	 */
	private LogicalIterator getLogical(XDMIterator expr){
		if(expr instanceof LogicalIterator){
			return (LogicalIterator)expr;
		}else{
			return null;
		}
	}
	
//	/**
//	 * Removes boolean iterator
//	 * @param expr
//	 * @return
//	 */
//	private Iterator removeBooleanIterator(Iterator expr){
//		if(expr instanceof BooleanIterator){
//			return expr.getAllSubIters()[0];
//		}else{
//			return expr;
//		}
//	}
}
